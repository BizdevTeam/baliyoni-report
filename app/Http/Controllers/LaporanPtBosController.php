<?php

namespace App\Http\Controllers;

use App\Models\LaporanPtBos;
use App\Traits\DateValidationTrait;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanPtBosController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
    
        $laporanptboss = LaporanPtBos::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);
    
        if ($request->ajax()) {
            return response()->json(['laporanptboss' => $laporanptboss]);
        }
    
        return view('hrga.laporanptbos', compact('laporanptboss'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pekerjaan' => 'required|string',
                'kondisi_bulanlalu' => 'required|string',
                'kondisi_bulanini' => 'required|string',
                'update' => 'required|string',
                'rencana_implementasi' => 'required|string',
                'keterangan' => 'required|string'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            LaporanPtBos::create($validatedData);
    
            return redirect()->route('laporanptbos.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing PT BOS Data: ' . $e->getMessage());
            return redirect()->route('laporanptbos.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanPtBos $laporanptbo)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pekerjaan' => 'required|string',
                'kondisi_bulanlalu' => 'required|string',
                'kondisi_bulanini' => 'required|string',
                'update' => 'required|string',
                'rencana_implementasi' => 'required|string',
                'keterangan' => 'required|string'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            $laporanptbo->update($validatedData);
    
            return redirect()->route('laporanptbos.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating PT BOS Data: ' . $e->getMessage());
            return redirect()->route('laporanptbos.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Laporan PT BOS|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Pekerjaan</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Kondisi Bulan Ini</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Kondisi Bulan Lalu</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Update</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Rencana Implementasi</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Keterangan</th>
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
                ->header('Content-Disposition', 'attachment; filename=\"laporan_PT_BOS.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanPtBos $laporanptbo)
    {
        $laporanptbo->delete();

        return redirect()->route('laporanptbos.index')->with('success', 'Data Berhaisil Dihapus');
    }

}
