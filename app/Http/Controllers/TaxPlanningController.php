<?php

namespace App\Http\Controllers;

use App\Models\TaxPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;

class TaxPlanningController extends Controller
{
    // Fungsi untuk mengambil dan menyimpan data dari API eksternal
    public function fetchTaxPlanningDataFromApi()
    {
        $baseUrl = 'https://bali.arpro.id/api';
        $endpoint = '/getTax';

        $url = $baseUrl . $endpoint;

        // Gunakan Guzzle untuk HTTP request
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);

        Log::info('Response from API: ' . $response->getBody());        

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);

            foreach ($data as $rec) {
                // Gunakan tanggal/waktu server saat ini untuk 'tanggal'
                $now = Carbon::now()->toDateTimeString();

                TaxPlanning::updateOrCreate(
                    [
                        'nama_perusahaan' => $rec['nama'],
                        'tanggal'         => $now,
                    ],
                    [
                        'tanggal'         => $now,
                        'tax_planning'    => intval($rec['tax_planning']),
                        'total_penjualan' => intval($rec['limit_penjualan']),
                    ]
                );
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil diambil dan disimpan.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data dari API.'], 500);
        }
        // $url = "https://bali.arpro.id/api/getTax";
        // $response = Http::timeout(5)->get($url);

        // if ($response->successful()) {
        //     foreach ($response->json() as $rec) {
        //         // Gunakan tanggal/waktu server saat ini untuk 'tanxggal'
        //         $now = Carbon::now()->toDateTimeString();

        //         TaxPlanning::updateOrCreate(
        //             [
        //                 'nama_perusahaan' => $rec['nama'],
        //                 'tanggal'         => $now,
        //             ],
        //             [
        //                 'tanggal'         => $now,
        //                 'tax_planning'    => intval($rec['tax_planning']),
        //                 'total_penjualan' => intval($rec['limit_penjualan']),
        //             ]
        //         );
        //     }
        //     return response()->json(['success' => true, 'message' => 'Data berhasil diambil dan disimpan.']);
        // } else {
        //     return response()->json(['success' => false, 'message' => 'Gagal mengambil data dari API.'], 500);
        // }
    }

    // public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 200);
    //     $search  = $request->input('search');

    //     // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
    //     $baseQuery = TaxPlanning::query();

    //     // Filter berdasarkan tanggal jika ada
    //     if (!empty($search)) {
    //         $baseQuery->where('tanggal', 'LIKE', "%$search%");
    //     }

    //     // Filter berdasarkan range bulan-tahun jika keduanya diisi
    //     if (!empty($startMonth) && !empty($endMonth)) {
    //         try {
    //             $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
    //             $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
    //             $baseQuery->whereBetween('tanggal', [$startDate, $endDate]);
    //         } catch (Exception $e) {
    //             return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
    //         }
    //     }
    //     // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
    //     $rekappenjualans = (clone $baseQuery)->paginate($perPage);

    //     $allData = $baseQuery->get();

    //     $allDatasets = []; // Array untuk menyimpan semua dataset chart

    //     $groupedByCompany = $allData->groupBy('nama_perusahaan');

    //     $chartPerPage = $request->input('chart_per_page', 5); // Default 5 perusahaan per halaman chart
    //     $chartPerPage = max(5, floor($chartPerPage / 5) * 5);

    //     $chartPage    = $request->input('chart_page', 1);

    //     $companyNames = $groupedByCompany->keys()->toArray();
    //     $totalCompanies = count($companyNames);
    //     $totalPages = ceil($totalCompanies / $chartPerPage);

    //     $offset = ($chartPage - 1) * $chartPerPage;
    //     $paginatedCompanyNames = array_slice($companyNames, $offset, $chartPerPage);

    //     $taxPlanningDataForChart = [];
    //     $totalPenjualanDataForChart = [];

    //     foreach ($paginatedCompanyNames as $companyName) { // Iterasi melalui nama perusahaan yang sudah di-paginasi
    //         $companyItems = $groupedByCompany[$companyName]; // Dapatkan item untuk perusahaan saat ini

    //         // Hitung total tax_planning dan total_penjualan untuk perusahaan ini
    //         $taxPlanningDataForChart[] = $companyItems->sum('tax_planning');
    //         $totalPenjualanDataForChart[] = $companyItems->sum('total_penjualan');
    //     }

    //     $allDatasets = [
    //         [
    //             'label'           => 'Total Tax Planning',
    //             'data'            => $taxPlanningDataForChart,
    //             'backgroundColor' => 'rgba(54, 162, 235, 0.7)', // Warna Biru Statis
    //         ],
    //         [
    //             'label'           => 'Total Sales',
    //             'data'            => $totalPenjualanDataForChart,
    //             'backgroundColor' => 'rgba(255, 159, 64, 0.7)', // Warna Oranye Statis
    //         ]
    //     ];

    //     $chartData = [
    //         'labels'   => $paginatedCompanyNames, // Label sekarang adalah nama perusahaan
    //         'datasets' => $allDatasets,
    //     ];
    //     $allTableDataForExport = $allData;
    //     $fullCompanyNames = $companyNames; // Ambil semua nama perusahaan
    //     $fullTaxPlanningData = [];
    //     $fullTotalPenjualanData = [];

    //     foreach ($fullCompanyNames as $companyName) {
    //         $companyItems = $groupedByCompany[$companyName];
    //         $fullTaxPlanningData[] = $companyItems->sum('tax_planning');
    //         $fullTotalPenjualanData[] = $companyItems->sum('total_penjualan');
    //     }

    //     $chartDataForExport = [
    //         'labels' => $fullCompanyNames,
    //         'datasets' => [
    //             ['label' => 'Total Tax Planning', 'data' => $fullTaxPlanningData, 'backgroundColor' => 'rgba(54, 162, 235, 0.7)'],
    //             ['label' => 'Total Sales', 'data' => $fullTotalPenjualanData, 'backgroundColor' => 'rgba(255, 159, 64, 0.7)']
    //         ]
    //     ];

    //     // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
    //     return view(
    //         'accounting.taxplaning',
    //         compact(
    //             'rekappenjualans',
    //             'perPage',
    //             'search',
    //             'chartData',
    //             'chartPage',        // Untuk paginasi chart
    //             'chartPerPage',     // Untuk paginasi chart
    //             'totalPages',
    //             'chartDataForExport',
    //             'allTableDataForExport',
    //             'totalCompanies'    // Untuk paginasi chart
    //         )
    //     );
    // }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 200);
        $search  = $request->input('search');

        // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
        $baseQuery = TaxPlanning::query();

        // Filter berdasarkan tanggal jika ada
        if (!empty($search)) {
            $baseQuery->where('tanggal', 'LIKE', "%$search%");
        }

        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $baseQuery->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
            }
        }
        // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
        $rekappenjualans = (clone $baseQuery)->paginate($perPage);

        $allData = $baseQuery->get();

        $allDatasets = []; // Array untuk menyimpan semua dataset chart

        $groupedByCompany = $allData->groupBy('nama_perusahaan');

        $chartPerPage = $request->input('chart_per_page', 5); // Default 5 perusahaan per halaman chart
        $chartPerPage = max(5, floor($chartPerPage / 5) * 5);

        $chartPage    = $request->input('chart_page', 1);

        $companyNames = $groupedByCompany->keys()->toArray();
        $totalCompanies = count($companyNames);
        $totalPages = ceil($totalCompanies / $chartPerPage);

        $offset = ($chartPage - 1) * $chartPerPage;
        $paginatedCompanyNames = array_slice($companyNames, $offset, $chartPerPage);

        $taxPlanningDataForChart = [];
        $totalPenjualanDataForChart = [];

        foreach ($paginatedCompanyNames as $companyName) { // Iterasi melalui nama perusahaan yang sudah di-paginasi
            $companyItems = $groupedByCompany[$companyName]; // Dapatkan item untuk perusahaan saat ini

            // Hitung total tax_planning dan total_penjualan untuk perusahaan ini
            $taxPlanningDataForChart[] = $companyItems->sum('tax_planning');
            $totalPenjualanDataForChart[] = $companyItems->sum('total_penjualan');
        }

        $allDatasets = [
            [
                'label'           => 'Total Tax Planning',
                'data'            => $taxPlanningDataForChart,
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)', // Warna Biru Statis
            ],
            [
                'label'           => 'Total Sales',
                'data'            => $totalPenjualanDataForChart,
                'backgroundColor' => 'rgba(255, 159, 64, 0.7)', // Warna Oranye Statis
            ]
        ];

        $chartData = [
            'labels'   => $paginatedCompanyNames, // Label sekarang adalah nama perusahaan
            'datasets' => $allDatasets,
        ];
        $allTableDataForExport = $allData;
        $fullCompanyNames = $companyNames; // Ambil semua nama perusahaan
        $fullTaxPlanningData = [];
        $fullTotalPenjualanData = [];

        foreach ($fullCompanyNames as $companyName) {
            $companyItems = $groupedByCompany[$companyName];
            $fullTaxPlanningData[] = $companyItems->sum('tax_planning');
            $fullTotalPenjualanData[] = $companyItems->sum('total_penjualan');
        }

        $chartDataForExport = [
            'labels' => $fullCompanyNames,
            'datasets' => [
                ['label' => 'Total Tax Planning', 'data' => $fullTaxPlanningData, 'backgroundColor' => 'rgba(54, 162, 235, 0.7)'],
                ['label' => 'Total Sales', 'data' => $fullTotalPenjualanData, 'backgroundColor' => 'rgba(255, 159, 64, 0.7)']
            ]
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            $aiInsight = $this->generateTaxPlanningInsight($allData, $chartData);
        }
        return view(
            'accounting.taxplaning',
            compact(
                'rekappenjualans',
                'perPage',
                'search',
                'chartData',
                'chartPage',        // Untuk paginasi chart
                'chartPerPage',     // Untuk paginasi chart
                'totalPages',
                'chartDataForExport',
                'allTableDataForExport',
                'totalCompanies',    // Untuk paginasi chart
                'aiInsight'    // Untuk paginasi chart
            )
        );
    }
   
    private function generateTaxPlanningInsight($reportData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) { return 'Layanan AI tidak terkonfigurasi dengan benar.'; }
        if ($reportData->isEmpty()) { return 'Tidak ada data yang cukup untuk dianalisis.'; }

        try {
            $analysisData = [
                'companies'         => $chartData['labels'],
                'tax_planning_data' => $chartData['datasets'][0]['data'],
                'sales_data'        => $chartData['datasets'][1]['data'],
                'total_tax_planning'=> $reportData->sum('tax_planning'),
                'total_sales'       => $reportData->sum('total_penjualan'),
                'company_count'     => $reportData->groupBy('nama_perusahaan')->count(),
            ];
            
            $prompt = $this->createTaxPlanningAnalysisPrompt($analysisData);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [ 'temperature' => 0.7, 'maxOutputTokens' => 1024 ],
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


    private function createTaxPlanningAnalysisPrompt(array $data): string
    {
        $total_tax_planning = number_format($data['total_tax_planning'], 0, ',', '.');
        $total_sales = number_format($data['total_sales'], 0, ',', '.');

        $companyDetailsStr = '';
        if($data['companies'] instanceof \Illuminate\Support\Collection) {
            $companies = $data['companies']->all();
        } else {
            $companies = $data['companies'];
        }

        for ($i = 0; $i < count($companies); $i++) {
            $company = $companies[$i];
            $tax = $data['tax_planning_data'][$i];
            $sales = $data['sales_data'][$i];
            $efficiency = ($tax > 0) ? ($sales / $tax) : 0;
            $companyDetailsStr .= "- **{$company}**: Biaya Tax Planning Rp " . number_format($tax) . ", menghasilkan Penjualan Rp " . number_format($sales) . ". (Efisiensi: " . number_format($efficiency, 2) . "x)\n";
        }

        return <<<PROMPT
            Anda adalah seorang Konsultan Pajak dan Strategi Bisnis senior yang bertugas memberikan insight kepada dewan direksi sebuah holding company.

            Berikut adalah data perbandingan antara biaya Perencanaan Pajak (Tax Planning) dengan Total Penjualan yang dihasilkan oleh masing-masing anak perusahaan.
            - Total Biaya Tax Planning (Semua Perusahaan): Rp {$total_tax_planning}
            - Total Penjualan (Semua Perusahaan): Rp {$total_sales}
            - Jumlah Perusahaan yang Dianalisis: {$data['company_count']}

            **Rincian Performa per Anak Perusahaan:**
            {$companyDetailsStr}
            *Catatan: Efisiensi dihitung dari (Total Penjualan / Biaya Tax Planning). Semakin tinggi, semakin baik.*

            **Tugas Anda:**
            Buat laporan analisis strategis (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan tajam.

            Analisis harus fokus pada efektivitas biaya tax planning terhadap pendapatan.
            1.  **Analisis Performa Unggulan & Tertinggal:** Identifikasi 1-2 perusahaan dengan **efisiensi tax planning terbaik** (penjualan tertinggi dengan biaya terendah). Identifikasi juga 1-2 perusahaan dengan **efisiensi terendah** (biaya tinggi, penjualan tidak sepadan).
            2.  **Identifikasi Pola:** Apakah ada pola yang terlihat? Misalnya, apakah perusahaan dengan biaya tax planning terbesar juga menghasilkan penjualan terbesar? Atau apakah ada "hidden gem", yaitu perusahaan dengan biaya rendah namun hasilnya maksimal?
            3.  **Rekomendasi Strategis & Alokasi Anggaran:** Berdasarkan analisis efisiensi, berikan 2-3 poin rekomendasi konkret. Contoh: 'Strategi tax planning dari PT. ABC (efisiensi tertinggi) perlu dipelajari dan diadopsi sebagai 'best practice' untuk anak perusahaan lain.' atau 'Lakukan audit mendalam pada alokasi biaya tax planning di PT. XYZ (efisiensi terendah) untuk memastikan anggaran digunakan secara efektif.'
            4.  **Kesimpulan Umum:** Berikan kesimpulan mengenai apakah secara umum biaya tax planning yang dikeluarkan oleh holding sudah sepadan dengan hasil penjualan yang didapat.

            Gunakan format markdown untuk poin-poin agar mudah dibaca.
            PROMPT;
    }

    public function exportPDF(Request $request)
    {
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set("memory_limit", "512M");

        try {
            $data = $request->validate([
                'table' => 'required|array',
                'chart' => 'required|string',
            ]);

            $tableRowsArray = $data['table'];    // array of "<tr>...</tr>"
            $chartBase64    = $data['chart'];

            if (empty($tableRowsArray)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }

            // **Implode jadi satu string HTML**
            $tableRowsHtml = implode('', $tableRowsArray);

            $mpdf = new Mpdf([
                'orientation'   => 'L',
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 35,
                'margin_bottom' => 10,
                'format'        => 'A4',
            ]);

            $headerImagePath = public_path('images/HEADER.png');
            $mpdf->SetHTMLHeader("
            <div style='position:absolute; top:0; left:0; width:100%; z-index:-1;'>
                <img src='{$headerImagePath}' style='width:100%;' />
            </div>
        ", 'O');

            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Tax Planning Report|');

            // **Gunakan $tableRowsHtml di sini, bukan $tableRowsArray**
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background:#f2f2f2;'>
                            <th style='padding:4px;'>Date</th>
                            <th style='padding:4px;'>Company</th>
                            <th style='padding:4px;'>Tax (Rp)</th>
                            <th style='padding:4px;'>Sales (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableRowsHtml}
                        </tbody>
                    </table>
                </div>
          <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Tax Planning Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
        </div>";

            $mpdf->WriteHTML($htmlContent);

            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan.pdf"');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
}
