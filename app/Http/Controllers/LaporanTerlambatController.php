<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanTerlambat;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanTerlambatController extends Controller
{
    use DateValidationTrait;
    // // Show the view
    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     #$query = KasHutangPiutang::query();

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $laporanterlambats = LaporanTerlambat::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('tanggal', 'LIKE', "%$search%")
    //                          ->orWhere('nama', 'like', "%$search%");
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalPenjualan = $laporanterlambats->sum('total_terlambat');

    //     // Siapkan data untuk chart
    //     function getRandomRGBA($opacity = 0.7) {
    //         return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //     }
        
    //     $labels = $laporanterlambats->map(function($item) {
    //         $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //         return $item->nama. ' - ' .$formattedDate;
    //     })->toArray();
    //     $data = $laporanterlambats->pluck('total_terlambat')->toArray();
        
    //     // Generate random colors for each data item
    //     $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
    //     $chartData = [
    //         'labels' => $labels, // Labels untuk chart
    //         'datasets' => [
    //             [
    //                 'label' => 'Grafik Laporan Terlambat', // Nama dataset
    //                 'text' => 'Total Terlambat', // Nama dataset
    //                 'data' => $data, // Data untuk chart
    //                 'backgroundColor' => $backgroundColors, // Warna batang random
    //             ],
    //         ],
    //     ];
        
    //     $aiInsight = null;
    //     if ($request->has('generate_ai')) {
    //         // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
    //         $aiInsight = $this->generateSickLeaveInsight($allSickReports, $chartData);
    //     }

        
    //     return view('hrga.laporanterlambat', compact('laporanterlambats', 'chartData','aiInsight'));    }
     public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanTerlambat::query()
            ->when($search, function ($query, $search) {
                $query->where('tanggal', 'LIKE', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
            });

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allLatenessReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporanterlambats = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allLatenessReports->map(function($item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->nama. ' - ' .$formattedDate;
        })->all();
        
        // [FIX] Gunakan kolom 'total_terlambat'
        $data = $allLatenessReports->pluck('total_terlambat')->all();
        
        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Laporan Terlambat',
                'text' => 'Total Keterlambatan',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];
        
        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateTardinessInsight($allLatenessReports, $chartData);
        }
            
        return view('hrga.laporanterlambat', compact('laporanterlambats', 'chartData', 'aiInsight'));
    }

    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Terlambat.
     */
    private function generateTardinessInsight($tardinessData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) { /* ... error handling ... */ }
        if ($tardinessData->isEmpty()) {
            return 'Tidak ada data keterlambatan yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai
            $analysisData = [
                'periods_and_employees' => $chartData['labels'],
                'tardiness_values'    => $chartData['datasets'][0]['data'],
                'total_tardiness'     => $tardinessData->sum('total_terlambat'),    // Menggunakan 'total_terlambat'
                'average_tardiness'   => $tardinessData->avg('total_terlambat'),     // Menggunakan 'total_terlambat'
                'max_tardiness'       => $tardinessData->max('total_terlambat'),      // Menggunakan 'total_terlambat'
                'data_count'          => $tardinessData->count(),
                // [BARU] Menambahkan data agregat per karyawan untuk analisis yang lebih baik
                'tardiness_per_employee' => $tardinessData->groupBy('nama')->map->sum('total_terlambat')->all(),
            ];
            
            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createTardinessAnalysisPrompt($analysisData);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [ 'temperature' => 0.7, 'maxOutputTokens' => 800 ],
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
        private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    /**
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Terlambat (HR).
     */
    private function createTardinessAnalysisPrompt(array $data): string
    {
        $total_tardiness = number_format($data['total_tardiness'], 0, ',', '.');
        $average_tardiness = number_format($data['average_tardiness'], 2, ',', '.');
        
        $employeeValuesStr = '';
        // Urutkan dari yang paling sering terlambat
        arsort($data['tardiness_per_employee']);
        foreach ($data['tardiness_per_employee'] as $employee => $total) {
            $employeeValuesStr .= "- **{$employee}**: " . number_format($total, 0, ',', '.') . " kali/menit keterlambatan\n";
        }

        return <<<PROMPT
            Anda adalah seorang Manajer Sumber Daya Manusia (HR Manager) yang tegas namun bijaksana, fokus pada peningkatan disiplin dan produktivitas tim.

            Berikut adalah data rekapitulasi keterlambatan karyawan (bisa dalam jumlah kejadian atau total menit) dalam periode waktu tertentu.
            - Total Keterlambatan Seluruh Karyawan: {$total_tardiness}
            - Rata-rata Keterlambatan per Laporan: {$average_tardiness}
            - Jumlah Total Laporan Keterlambatan: {$data['data_count']}

            **Akumulasi Total Keterlambatan per Karyawan (Diurutkan dari Terbanyak):**
            {$employeeValuesStr}

            **Tugas Anda:**
            Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan konstruktif untuk manajemen.

            Analisis harus fokus pada identifikasi pola keterlambatan dan memberikan solusi untuk meningkatkan kedisiplinan.
            1.  **Analisis Tren Keterlambatan:** Jelaskan tren umum keterlambatan. Apakah ada pola hari atau bulan tertentu dimana tingkat keterlambatan meningkat? (misal: "Tingkat keterlambatan cenderung meningkat signifikan pada hari Senin.").
            2.  **Identifikasi Karyawan yang Memerlukan Pembinaan:** Identifikasi karyawan dengan tingkat keterlambatan tertinggi. Sarankan pendekatan pembinaan, bukan hukuman. Contoh: "Perlu dilakukan sesi diskusi personal dengan Sdr. Budi untuk memahami kendala yang dihadapinya, apakah terkait transportasi, masalah pribadi, atau motivasi kerja, dan mencari solusi bersama."
            3.  **Rekomendasi Kebijakan & Peningkatan Disiplin:** Berikan 2-3 poin rekomendasi konkret. Contoh: 'Tegakkan kembali aturan mengenai jam kerja secara konsisten dan transparan.' atau 'Pertimbangkan untuk memberikan insentif kecil (misal: voucher sarapan) bagi karyawan atau tim yang berhasil mencatatkan nol keterlambatan selama sebulan.' atau 'Implementasikan sistem jam kerja fleksibel (flexi-time) untuk mengurangi dampak kemacetan lalu lintas.'
            4.  **Dampak pada Budaya & Kinerja:** Berikan komentar singkat mengenai bagaimana tingkat keterlambatan dapat mempengaruhi moral tim, keadilan, dan alur kerja, terutama untuk peran yang bergantung pada kehadiran tepat waktu.

            Gunakan format markdown untuk poin-poin agar mudah dibaca.
            PROMPT;
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_terlambat' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan nama
            $exists = LaporanTerlambat::where('tanggal', $validatedData['tanggal'])
            ->where('nama', $validatedData['nama'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanTerlambat::create($validatedData);
    
            return redirect()->route('laporanterlambat.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Laporan Terlambat Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('laporanterlambat.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_terlambat' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanTerlambat::where('nama', $validatedData['nama'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanTerlambat::create($validatedData);
    
            return redirect()->route('laporanterlambat.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Terlambat: ' . $e->getMessage());
            return redirect()->route('laporanterlambat.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanTerlambat $laporanterlambat)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_terlambat' => 'required|integer|min:0',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan nama
            $exists = LaporanTerlambat::where('nama', $validatedData['nama'])
                ->where('id_terlambat', '!=', $laporanterlambat->id_terlambat)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $laporanterlambat->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('laporanterlambat.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Terlambat: ' . $e->getMessage());
            return redirect()->route('laporanterlambat.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Late Arrival Report|');
            
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Employee</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Late Arrival Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Late Arrival Chart</h2>
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

    public function destroy(LaporanTerlambat $laporanterlambat)
    {
        try {
            $laporanterlambat->delete();
            return redirect()->route('laporanterlambat.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Terlambat Data: ' . $e->getMessage());
            return redirect()->route('laporanterlambat.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = LaporanTerlambat::query();
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
        
        $laporanterlambats = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Siapkan data untuk chart
        $labels = $laporanterlambats->pluck('nama')->toArray();
        $data = $laporanterlambats->pluck('total_terlambat')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);
    
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Terlambat',
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
        $query = LaporanTerlambat::query();
        
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
        
        $laporanterlambats = $query->get();
    
        // Akumulasi total_sakit berdasarkan bulan
        $akumulasiData = [];
        foreach ($laporanterlambats as $item) {
            $bulan = \Carbon\Carbon::parse($item->tanggal)->format('F Y');
            if (!isset($akumulasiData[$bulan])) {
                $akumulasiData[$bulan] = 0;
            } 
            $akumulasiData[$bulan] += $item->total_terlambat;
        }
    
        // Siapkan data untuk chart
        $labels = array_keys($akumulasiData);
        $data = array_values($akumulasiData);
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);
    
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Terlambat per Bulan',
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

