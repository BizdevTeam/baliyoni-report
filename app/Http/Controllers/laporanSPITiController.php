<?php

namespace App\Http\Controllers;

use App\Models\LaporanSPITI;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class laporanSPITiController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanSPITI::query();
    
        // Filter berdasarkan tanggal jika ada
        if (!empty($search)) {
            $query->where('tanggal', 'LIKE', "%$search%");
        }
    
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
            }
        }
        // Ambil data dengan pagination
        $laporanspitis = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json(['laporanspitis' => $laporanspitis]);
        }

        return view("spi.laporanspiti" , compact("laporanspitis"));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi' => 'required',
                'implementasi' => 'required',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            LaporanSPITI::create($validatedData);
            return redirect()->route('laporanspiti.index')->with('success', 'Laporan SPITI berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSPITI $laporanspiti)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi' => 'required',
                'implementasi' => 'required',
            ]);
            
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $laporanspiti->update($validatedData);
            return redirect()->route('laporanspiti.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error( 'Kesalahan laporanspi update data' . $e->getMessage());
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
            ]);
    
            // Ambil data dari request
            $tableHTML = trim($data['table']);
    
            // Validasi isi tabel untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
    
            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 10, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);
    
            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan SPI - Laporan SPI IT|');
    
            // Set CSS untuk memastikan formatting CKEditor dipertahankan
            $styleCSS = "
            ul, ol {
                padding-left: 20px;
                margin: 5px 0;
            }
            li {
                margin-bottom: 3px;
            }
            p {
                margin: 5px 0;
            }
            strong, b {
                font-weight: bold;
            }
            em, i {
                font-style: italic;
            }
        ";
        
            // Buat konten tabel dengan style tambahan untuk CKEditor
            $htmlContent = "
                <style>
                    {$styleCSS}
                </style>
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Aspek</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Masalah</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Solusi</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Implementasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
            ";

            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
            
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_rekap_penjualan.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanSPITI $laporanspiti)
    {
        try {
            $laporanspiti->delete();
            return redirect()->route('laporanspiti.index')->with('success','Data berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
    
}
