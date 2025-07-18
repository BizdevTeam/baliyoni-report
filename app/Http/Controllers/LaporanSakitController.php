<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanSakit;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanSakitController extends Controller
{
    use DateValidationTrait;
    // Show the view
    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     #$query = KasHutangPiutang::query();

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $laporansakits = LaporanSakit::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('tanggal', 'LIKE', "%$search%")
    //                          ->orWhere('nama', 'like', "%$search%");
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalPenjualan = $laporansakits->sum('total_sakit');

    //     // Siapkan data untuk chart
    //     function getRandomRGBA($opacity = 0.7) {
    //         return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //     }

    //     $labels = $laporansakits->map(function($item) {
    //         $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //         return $item->nama. ' - ' .$formattedDate;
    //     })->toArray();
    //     $data = $laporansakits->pluck('total_sakit')->toArray();

    //     // Generate random colors for each data item
    //     $backgroundColors = array_map(fn() => getRandomRGBA(), $data);

    //     $chartData = [
    //         'labels' => $labels, // Labels untuk chart
    //         'datasets' => [
    //             [
    //                 'label' => 'Grafik Laporan Sakit', // Employee dataset
    //                 'text' => 'Sick Leave Total', // Employee dataset
    //                 'data' => $data, // Data untuk chart
    //                 'backgroundColor' => $backgroundColors, // Warna batang random
    //             ],
    //         ],
    //     ];
    //           $aiInsight = null;
    //         if ($request->has('generate_ai')) {
    //             // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
    //             $aiInsight = $this->generateServiceRevenueInsight($allServiceReports, $chartData);
    //         }

    //     return view('hrga.laporansakit', compact('laporansakits', 'chartData'));  
    //   }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanSakit::query()
            ->when($search, function ($query, $search) {
                $query->where('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allSickReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporansakits = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allSickReports->map(function ($item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->nama . ' - ' . $formattedDate;
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

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateSickLeaveInsight($allSickReports, $chartData);
        }

        return view('hrga.laporansakit', compact('laporansakits', 'chartData', 'aiInsight'));
    }

    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Sakit.
     */
    private function generateSickLeaveInsight($sickLeaveData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) { /* ... error handling ... */
        }
        if ($sickLeaveData->isEmpty()) {
            return 'Tidak ada data absensi sakit yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai
            $analysisData = [
                'periods_and_employees' => $chartData['labels'],
                'sick_leave_values'   => $chartData['datasets'][0]['data'],
                'total_sick_days'     => $sickLeaveData->sum('total_sakit'),    // Menggunakan 'total_sakit'
                'average_sick_days'   => $sickLeaveData->avg('total_sakit'),     // Menggunakan 'total_sakit'
                'max_sick_days'       => $sickLeaveData->max('total_sakit'),      // Menggunakan 'total_sakit'
                'data_count'          => $sickLeaveData->count(),
                // [BARU] Menambahkan data agregat per karyawan untuk analisis yang lebih baik
                'sick_days_per_employee' => $sickLeaveData->groupBy('nama')->map->sum('total_sakit')->all(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createSickLeaveAnalysisPrompt($analysisData);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 800],
                ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text', 'Tidak dapat menghasilkan insight dari AI.');
            }

            Log::error('Gemini API error: ' . $response->body());
            return 'Gagal menghubungi layanan analisis AI.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Sakit (HR).
     */
    private function createSickLeaveAnalysisPrompt(array $data): string
    {
        $total_sick_days = number_format($data['total_sick_days'], 0, ',', '.');
        $average_sick_days = number_format($data['average_sick_days'], 2, ',', '.');

        $employeeValuesStr = '';
        // Urutkan dari yang paling sering sakit
        arsort($data['sick_days_per_employee']);
        foreach ($data['sick_days_per_employee'] as $employee => $total) {
            $employeeValuesStr .= "- **{$employee}**: " . number_format($total, 0, ',', '.') . " hari\n";
        }

        return <<<PROMPT
Anda adalah seorang Manajer Sumber Daya Manusia (HR Manager) yang peduli pada kesehatan dan kesejahteraan karyawan.

Berikut adalah data rekapitulasi jumlah hari sakit karyawan dalam periode waktu tertentu.
- Total Hari Sakit Seluruh Karyawan: {$total_sick_days} hari
- Rata-rata Hari Sakit per Entri Laporan: {$average_sick_days} hari
- Jumlah Total Laporan Sakit: {$data['data_count']}

**Akumulasi Total Hari Sakit per Karyawan (Diurutkan dari Terbanyak):**
{$employeeValuesStr}

**Tugas Anda:**
Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan empatik untuk manajemen.

Analisis harus fokus pada tren kesehatan karyawan dan identifikasi potensi masalah, bukan untuk menyalahkan individu.
1.  **Analisis Tren Kesehatan Karyawan:** Jelaskan tren umum dari absensi sakit. Apakah ada peningkatan pada bulan-bulan tertentu? Jika ya, berikan hipotesis (misal: "Terjadi lonjakan absensi sakit pada bulan Juli-Agustus, yang mungkin berkaitan dengan musim flu tahunan.").
2.  **Identifikasi Karyawan yang Membutuhkan Perhatian:** Tanpa nada menuduh, identifikasi karyawan dengan jumlah hari sakit yang signifikan di atas rata-rata. Sarankan pendekatan yang suportif. Contoh: "Perlu adanya perhatian khusus untuk Sdr. Budi dan Sdri. Wati yang memiliki jumlah absensi sakit tertinggi. Ini bisa menjadi indikator adanya kebutuhan dukungan kesehatan."
3.  **Rekomendasi Program Kesejahteraan (Wellness Program):** Berikan 2-3 poin rekomendasi konkret untuk meningkatkan kesehatan karyawan dan mengurangi absensi sakit. Contoh: 'Adakan program vaksinasi flu gratis sebelum musim pancaroba.' atau 'Tingkatkan sosialisasi mengenai pentingnya istirahat dan manajemen stres.' atau 'Pertimbangkan untuk memberikan jatah 'cuti sehat' yang bisa digunakan tanpa surat dokter untuk mengurangi penyebaran penyakit di kantor.'
4.  **Dampak pada Produktivitas:** Berikan komentar singkat mengenai bagaimana tren absensi ini dapat mempengaruhi produktivitas tim dan sarankan langkah mitigasi.

Gunakan format markdown untuk poin-poin agar mudah dibaca.
PROMPT;
    }

    private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_sakit' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan nama
            $exists = LaporanSakit::where('tanggal', $validatedData['tanggal'])
                ->where('nama', $validatedData['nama'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanSakit::create($validatedData);

            return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_sakit' => 'required|integer',
                'nama' => 'required|string'
            ]);

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanSakit::where('nama', $validatedData['nama'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanSakit::create($validatedData);

            return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Rasio data: ' . $e->getMessage());
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSakit $laporansakit)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_sakit' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan nama
            $exists = LaporanSakit::where('nama', $validatedData['nama'])
                ->where('id_sakit', '!=', $laporansakit->id_sakit)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            // Update data
            $laporansakit->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()->route('laporansakit.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
                                <th style='border: 1px solid #000; padding: 2px;'>Employee</th>
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

    public function destroy(LaporanSakit $laporansakit)
    {
        try {
            $laporansakit->delete();
            return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data: ' . $e->getMessage());
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanSakit::query();
        // Filter berdasarkan tanggal jika ada
        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }

        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if ($startMonth && $endMonth) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();

            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $laporansakits = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Siapkan data untuk chart
        $labels = $laporansakits->pluck('nama')->toArray();
        $data = $laporansakits->pluck('total_sakit')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sick Leave Total',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];

        // Kembalikan data dalam format JSON
        return response()->json($chartData);
    }

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    public function chartTotal(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        // Ambil data dari database
        $query = LaporanSakit::query();

        // Filter berdasarkan tanggal jika ada
        if ($search) {
            $query->where('tanggal', 'LIKE', "%$search%");
        }

        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if ($startMonth && $endMonth) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();

            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $laporansakits = $query->get();

        // Akumulasi total_sakit berdasarkan bulan
        $akumulasiData = [];
        foreach ($laporansakits as $item) {
            $bulan = \Carbon\Carbon::parse($item->tanggal)->format('F Y');
            if (!isset($akumulasiData[$bulan])) {
                $akumulasiData[$bulan] = 0;
            }
            $akumulasiData[$bulan] += $item->total_sakit;
        }

        // Siapkan data untuk chart
        $labels = array_keys($akumulasiData);
        $data = array_values($akumulasiData);
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sick Leave Total per Bulan',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];

        // Kembalikan data dalam format JSON
        return response()->json($chartData);
    }

    private function getRandomRGBAA1($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
}
