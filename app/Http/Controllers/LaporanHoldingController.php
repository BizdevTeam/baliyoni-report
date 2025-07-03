<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHolding;
use App\Models\Perusahaan;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanHoldingController extends Controller
{
    use DateValidationTrait;

    //     public function index(Request $request)
    //     {
    //         $perusahaans = Perusahaan::all();

    //         $perPage = $request->input('per_page', 12);
    //         $search = $request->input('search');

    //         // Retrieve LaporanHolding records along with the related Perusahaan
    //         $laporanholdings = LaporanHolding::with('perusahaan')
    //             ->when($search, function ($query, $search) {
    //                 return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"])
    //                 ->orWhereHas('perusahaan', function ($q) use ($search) {
    //                                  $q->where('nama_perusahaan', 'LIKE', "%$search%");
    //                              });
    //             })
    //             ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
    //             ->paginate($perPage);

    //         // Prepare chart data
    //         $labels = $laporanholdings->map(function ($item) {
    //             // Format the month using Carbon’s translatedFormat() as defined in your model accessor
    //             $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //             return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
    //         })->toArray();
    //         $data = $laporanholdings->pluck('nilai')->toArray();

    //         // Generate random colors for each data point
    //         $backgroundColors = array_map(function () {
    //             return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 0.7);
    //         }, $data);

    //         $chartData = [
    //             'labels'   => $labels,
    //             'datasets' => [
    //                 [
    //                     'label'           => 'Purchase Report Chart',
    //                     'data'            => $data,
    //                     'backgroundColor' => $backgroundColors,
    //                 ],
    //             ],
    //         ];
    //         $aiInsight = null;

    //         if ($request->has('generate_ai')) {
    //         $aiInsight = $this->generateSalesInsight($laporanholdings, $chartData);
    //         }

    //         return view('procurements.laporanholding', compact('laporanholdings', 'chartData', 'perusahaans', 'aiInsight'))
    //             ->with('search', $search)
    //             ->with('perPage', $perPage);
    //     }
    // private function generateSalesInsight($salesData, $chartData)
    //     {
    //         // Ambil konfigurasi dari file config/services.php
    //         $apiKey = config('services.gemini.api_key');
    //         $apiUrl = config('services.gemini.api_url');

    //         if (!$apiKey || !$apiUrl) {
    //             Log::error('Gemini API Key or URL is not configured.');
    //             return 'Layanan AI tidak terkonfigurasi dengan benar.';
    //         }

    //         // Jangan panggil AI jika tidak ada data untuk dianalisis
    //         if ($salesData->isEmpty()) {
    //             return 'Tidak ada data penjualan yang cukup untuk dianalisis.';
    //         }

    //         try {
    //             $analysisData = [
    //                 'periods' => $chartData['labels'],
    //                 'sales_values' => $chartData['datasets'][0]['data'],
    //                 'total_sales' => $salesData->sum('total_penjualan'),
    //                 'average_sales' => $salesData->avg('total_penjualan'),
    //                 'max_sales' => $salesData->max('total_penjualan'),
    //                 'min_sales' => $salesData->min('total_penjualan'),
    //                 'data_count' => $salesData->count(),
    //             ];

    //             $prompt = $this->createAnalysisPrompt($analysisData);

    //             // Kirim request ke API Gemini dengan format yang BENAR
    //             $response = Http::withHeaders([
    //                 'Content-Type' => 'application/json',
    //             ])->post($apiUrl . '?key=' . $apiKey, [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             ['text' => $prompt]
    //                         ]
    //                     ]
    //                 ],
    //                 'generationConfig' => [
    //                     'temperature' => 0.7,
    //                     'maxOutputTokens' => 800, // Mungkin butuh token lebih banyak untuk analisis mendalam
    //                 ]
    //             ]);

    //             if ($response->successful()) {
    //                 // Parsing response dari Gemini
    //                 $result = $response->json();
    //                 return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
    //             } else {
    //                 Log::error('Gemini API error: ' . $response->body());
    //                 return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Error generating AI insight: ' . $e->getMessage());
    //             return 'Terjadi kesalahan dalam menghasilkan analisis.';
    //         }
    //     }

    //      private function createAnalysisPrompt($data)
    //     {
    //         $periods = implode(", ", $data['periods']);
    //         $values = implode(", ", array_map(fn($v) => 'Rp'.number_format($v,0,',','.'), $data['sales_values']));

    //         return "Anda adalah seorang analis bisnis dan data senior di sebuah perusahaan di Indonesia.

    //         Berikut adalah data rekap penjualan bulanan dalam Rupiah:
    //         - Periode Data: {$periods}
    //         - Rincian Penjualan per Bulan: {$values}
    //         - Total Penjualan Selama Periode: Rp " . number_format($data['total_sales'], 0, ',', '.') . "
    //         - Rata-rata Penjualan per Bulan: Rp " . number_format($data['average_sales'], 0, ',', '.') . "
    //         - Penjualan Tertinggi dalam Sebulan: Rp " . number_format($data['max_sales'], 0, ',', '.') . "
    //         - Penjualan Terendah dalam Sebulan: Rp " . number_format($data['min_sales'], 0, ',', '.') . "
    //         - Jumlah Data: {$data['data_count']} bulan

    //         Tugas Anda adalah membuat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan profesional untuk manajer. Laporan harus mencakup:
    //         1.  **Ringkasan Kinerja:** Jelaskan secara singkat tren penjualan (apakah naik, turun, atau fluktuatif).
    //         2.  **Identifikasi Puncak & Penurunan:** Sebutkan bulan dengan performa terbaik dan terburuk, serta berikan kemungkinan penyebabnya jika ada pola yang terlihat (misalnya, musim liburan, awal tahun, dll.).
    //         3.  **Rekomendasi Strategis:** Berikan 2-3 poin rekomendasi yang konkret dan bisa ditindaklanjuti untuk meningkatkan penjualan di bulan-bulan berikutnya. Contoh: 'Fokuskan promosi pada produk X di bulan Y' atau 'Evaluasi strategi pemasaran di bulan Z'.
    //         4.  **Proyeksi Singkat:** Berikan prediksi kualitatif (bukan angka pasti) untuk bulan berikutnya berdasarkan tren yang ada.

    //         Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.";
    //     }

    public function index(Request $request)
    {
        $perusahaans = Perusahaan::all();
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanHolding::with('perusahaan')
            ->when($search, function ($query, $search) {
                $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"])
                    ->orWhereHas('perusahaan', fn($q) => $q->where('nama_perusahaan', 'LIKE', "%{$search}%"));
            });

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allHoldingReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporanholdings = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allHoldingReports->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
        })->all();

        // [FIX] Gunakan kolom 'nilai'
        $data = $allHoldingReports->pluck('nilai')->all();

        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Holding Report Chart',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data (`$allHoldingReports`)
            // [FIX] Nama fungsi diubah agar lebih sesuai
            $aiInsight = $this->generateHoldingInsight($allHoldingReports, $chartData);
        }

        return view('procurements.laporanholding', compact('laporanholdings', 'chartData', 'perusahaans', 'aiInsight'))
            ->with('search', $search)
            ->with('perPage', $perPage);
    }
    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks.
     */
    private function generateHoldingInsight($reportData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($reportData->isEmpty()) {
            return 'Tidak ada data laporan yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data "Laporan Holding"
            $analysisData = [
                'periods_and_companies' => $chartData['labels'],
                'report_values'         => $chartData['datasets'][0]['data'],
                'total_value'           => $reportData->sum('nilai'),    // Menggunakan 'nilai'
                'average_value'         => $reportData->avg('nilai'),     // Menggunakan 'nilai'
                'max_value'             => $reportData->max('nilai'),      // Menggunakan 'nilai'
                'min_value'             => $reportData->min('nilai'),      // Menggunakan 'nilai'
                'data_count'            => $reportData->count(),
                // [BARU] Menambahkan data agregat per perusahaan untuk analisis yang lebih mendalam
                'value_per_company'     => $reportData->groupBy('perusahaan.nama_perusahaan')->map->sum('nilai')->all(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createHoldingAnalysisPrompt($analysisData);

            // ... sisa kode pemanggilan API tidak berubah ...
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 800,],
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
            }

            Log::error('Gemini API error: ' . $response->body());
            return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Holding.
     */
    private function createHoldingAnalysisPrompt(array $data): string
    {
        $periods = implode(', ', $data['periods_and_companies']);
        $values  = implode(', ', array_map(fn($v) => 'Rp' . number_format($v, 0, ',', '.'), $data['report_values']));
        $total_value = number_format($data['total_value'], 0, ',', '.');

        $companyValuesStr = '';
        foreach ($data['value_per_company'] as $company => $total) {
            $companyValuesStr .= "- **{$company}**: Rp " . number_format($total, 0, ',', '.') . "\n";
        }

        return <<<PROMPT
Anda adalah seorang Direktur Keuangan (CFO) di sebuah Holding Company yang menganalisis performa anak-anak perusahaan.

Berikut adalah data rekapitulasi "nilai" (bisa berupa profit, nilai proyek, atau aset) dari berbagai anak perusahaan per periode.
- Rincian Laporan (Perusahaan - Periode): {$periods}
- Rincian Nilai per Laporan: {$values}

**Ringkasan Statistik:**
- Total Nilai Gabungan: Rp {$total_value}
- Jumlah Laporan: {$data['data_count']}

**Akumulasi Nilai per Anak Perusahaan:**
{$companyValuesStr}

**Tugas Anda:**
Buat laporan analisis singkat dalam Bahasa Indonesia yang formal dan strategis untuk rapat dewan direksi. Fokus pada kesehatan portofolio perusahaan.

Analisis harus mencakup:
1.  **Analisis Performa Portofolio:** Identifikasi 2-3 anak perusahaan dengan kontribusi nilai tertinggi (mesin uang) dan 2-3 dengan kontribusi terendah (membutuhkan perhatian). Jelaskan signifikansi mereka terhadap total nilai holding.
2.  **Identifikasi Tren & Anomali:** Apakah ada perusahaan yang menunjukkan pertumbuhan nilai yang pesat atau sebaliknya, penurunan drastis? Berikan hipotesis singkat mengenai penyebabnya (misal: "PT. ABC menunjukkan lonjakan nilai, kemungkinan karena keberhasilan proyek baru...").
3.  **Rekomendasi Strategis untuk Holding:** Berikan 2-3 poin rekomendasi dari sudut pandang holding. Contoh: 'Pertimbangkan alokasi modal tambahan untuk PT. X yang sedang bertumbuh pesat.' atau 'Lakukan tinjauan operasional mendalam pada PT. Y yang kinerjanya stagnan.'
4.  **Kesimpulan Kesehatan Portofolio:** Berikan kesimpulan umum mengenai kesehatan portofolio perusahaan saat ini berdasarkan data.

Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.
PROMPT;
    }
    private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'perusahaan_id' => 'required|exists:perusahaans,id',
                'nilai' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan_id
            $exists = LaporanHolding::where('tanggal', $request->date)
                ->where('perusahaan_id', $request->perusahaan_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data untuk sudah ada');
            }

            LaporanHolding::create($validatedData);
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Holding Data: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanHolding $laporanholding)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'perusahaan_id' => 'required|exists:perusahaans,id',
                'nilai' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            // Cek apakah kombinasi date dan perusahaan_id sudah ada di data lain
            $exists = LaporanHolding::where('tanggal', $request->date)
                ->where('perusahaan_id', $request->perusahaan_id)
                ->where('id', '!=', $laporanholding->id) // Menggunakan model binding
                ->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data untuk sudah ada');
            }
    
            // Update data
            $laporanholding->update($validatedData);
    
            return redirect()->route('laporanholding.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanHolding $laporanholding)
    {
        try {
            $laporanholding->delete();

            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Holding Data: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF for the given laporanholding holding data.
     * Expects 'table' (HTML string) and 'chart' (base64 string) in the request.
     */
    public function exportPDF(Request $request)
    {
        try {
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);

            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);

            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }

            $mpdf = new \Mpdf\Mpdf([
                'orientation'  => 'L',
                'margin_left'  => 10,
                'margin_right' => 10,
                'margin_top'   => 35,
                'margin_bottom'=> 10,
                'format'       => 'A4',
            ]);

            $headerImagePath = public_path('images/HEADER.png');
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O');

            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Purchase Report (Holding)|');

            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Company</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Holding Value (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 60%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Purchase Report Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";

            $mpdf->WriteHTML($htmlContent);

            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_perusahaan.pdf"');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    /**
     * Return all LaporanHolding data with related Perusahaan for API consumption.
     */
    public function getLaporanHoldingData()
    {
        $data = LaporanHolding::with('perusahaan')->get(['tanggal', 'perusahaan_id', 'nilai']);
        return response()->json($data);
    }

    /**
     * Provide chart data in JSON format.
     */
    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanHolding::query();
        
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

        $laporanholdings = $query
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->get();

        $labels = $laporanholdings->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
        })->toArray();
        $data = $laporanholdings->pluck('nilai')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

        $chartData = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Total Paket',
                    'data'            => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];

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
    $query = LaporanHolding::query();
    
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
    
    $laporanholdings = $query->get();

    // Akumulasi total penjualan berdasarkan nama website
    $akumulasiData = [];
    foreach ($laporanholdings as $item) {
        $namaPerusahaan = $item->perusahaan->nama_perusahaan;
        if (!isset($akumulasiData[$namaPerusahaan])) {
            $akumulasiData[$namaPerusahaan] = 0;
        }
        $akumulasiData[$namaPerusahaan] += $item->nilai;
    }

    // Siapkan data untuk chart
    $labels = array_keys($akumulasiData);
    $data = array_values($akumulasiData);
    $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);

    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Total Paket',
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
