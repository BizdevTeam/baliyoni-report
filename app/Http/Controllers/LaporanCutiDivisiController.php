<?php

namespace App\Http\Controllers;

use App\Models\LaporanCutiDivisi;
use Illuminate\Http\Request;
use App\Traits\DateValidationTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanCutiDivisiController extends Controller
{
    use DateValidationTrait;

    private function getRandomRGBA($opacity = 0.7)
{
    return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
}
public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $query = LaporanCutiDivisi::query();

        if ($request->filled('start_date')) {
            try {
                // Directly use the date string from the request.
                $startDate = $request->start_date;
                $query->whereDate('tanggal', '>=', $startDate);
            } catch (Exception $e) {
                Log::error("Invalid start_date format provided: " . $request->start_date);
            }
        }

        if ($request->filled('end_date')) {
            try {
                // Directly use the date string from the request.
                $endDate = $request->end_date;
                $query->whereDate('tanggal', '<=', $endDate);
            } catch (Exception $e) {
                Log::error("Invalid end_date format provided: " . $request->end_date);
            }
        }

        // Order the results and paginate, ensuring the correct filter parameters are kept.
        $laporancutidivisis = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $laporancutidivisis->map(function ($item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->divisi . ' - ' . $formattedDate;
        })->all();

        // [FIX] Gunakan kolom 'total_cuti'
        $data = $laporancutidivisis->pluck('total_cuti')->all();

        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Laporan Sakit',
                'text' => 'Total Hari Sakit',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];

        // $aiInsight = null;
        // if ($request->has('generate_ai')) {
        //     // [FIX] Panggil AI dengan SEMUA data dan divisi fungsi yang sesuai
        //     $aiInsight = $this->generateSickLeaveInsight($laporancutidivisis, $chartData);
        // }

        return view('hrga.laporancutidivisi', compact('laporancutidivisis', 'chartData'));
    }

public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'divisi' => ['required', 'string', Rule::in(['Marketing', 'Procurement', 'Accounting', 'IT', 'HRGA', 'Support', 'SPI'])],
                'total_cuti' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            LaporanCutiDivisi::create($validatedData);

            return redirect()->route('laporancutidivisi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('laporancutidivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_cuti' => 'required|integer',
                'divisi' => 'required|string',
            ]);

            LaporanCutiDivisi::create($validatedData);

            return redirect()->route('laporancutidivisi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Rasio data: ' . $e->getMessage());
            return redirect()->route('laporancutidivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

public function update(Request $request, LaporanCutiDivisi $laporancutidivisi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'divisi' => ['required', 'string', Rule::in(['Marketing', 'Procurement', 'Accounting', 'IT', 'HRGA', 'Support', 'SPI'])],
                'total_cuti' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Update data
            $laporancutidivisi->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()->route('laporancutidivisi.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()->route('laporancutidivisi.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
public function destroy(LaporanCutiDivisi $laporancutidivisi)
    {
        try {
            $laporancutidivisi->delete();
            return redirect()->route('laporancutidivisi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data: ' . $e->getMessage());
            return redirect()->route('laporancutidivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);
    
            // Ambil data dari request
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);
    
            // Validasi isi tabel dan chart untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Annual Leave Report|');
            
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Divisi</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Annual Leave Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Annual Leave Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_perusahaan.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }   
    
}
