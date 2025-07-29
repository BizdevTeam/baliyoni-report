<?php

namespace App\Http\Controllers;

use App\Models\LaporanSakitDivisi;
use Illuminate\Http\Request;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanSakitDivisiController extends Controller
{
    use DateValidationTrait;

    private function getRandomRGBA($opacity = 0.7)
{
    return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
}
public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanSakitDivisi::query()
            ->when($search, function ($query, $search) {
                $query->where('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%");
            });

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allSickReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporansakitdivisis = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allSickReports->map(function ($item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->divisi . ' - ' . $formattedDate;
        })->all();

        // [FIX] Gunakan kolom 'total_sakit'
        $data = $allSickReports->pluck('total_sakit')->all();

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
        //     $aiInsight = $this->generateSickLeaveInsight($allSickReports, $chartData);
        // }

        return view('hrga.laporansakitdivisi', compact('laporansakitdivisis', 'chartData'));
    }

public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'divisi' => ['required', 'string', Rule::in(['Marketing', 'Procurement', 'Accounting', 'IT', 'HRGA', 'Support', 'SPI'])],
                'total_sakit' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan divisi
            $exists = LaporanSakitDivisi::where('tanggal', $validatedData['tanggal'])
                ->where('divisi', $validatedData['divisi'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanSakitDivisi::create($validatedData);

            return redirect()->route('laporansakitdivisi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('laporansakitdivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_sakit' => 'required|integer',
                'divisi' => 'required|string',
            ]);

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanSakitDivisi::where('divisi', $validatedData['divisi'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanSakitDivisi::create($validatedData);

            return redirect()->route('laporansakitdivisi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Rasio data: ' . $e->getMessage());
            return redirect()->route('laporansakitdivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

public function update(Request $request, LaporanSakitDivisi $laporansakitdivisi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'divisi' => ['required', 'string', Rule::in(['Marketing', 'Procurement', 'Accounting', 'IT', 'HRGA', 'Support', 'SPI'])],
                'total_sakit' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Update data
            $laporansakitdivisi->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()->route('laporansakitdivisi.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()->route('laporansakitdivisi.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
public function destroy(LaporanSakitDivisi $laporansakitdivisi)
    {
        try {
            $laporansakitdivisi->delete();
            return redirect()->route('laporansakitdivisi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data: ' . $e->getMessage());
            return redirect()->route('laporansakitdivisi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $mpdf = new Mpdf([
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Sick Leave Report|');

            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Divisi</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Sick Leave Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Sick Leave Chart</h2>
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
