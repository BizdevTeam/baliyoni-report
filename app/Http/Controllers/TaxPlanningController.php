<?php

namespace App\Http\Controllers;

use App\Models\TaxPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;

class TaxPlanningController extends Controller
{
    // Fungsi untuk mengambil dan menyimpan data dari API eksternal
    public function fetchTaxPlanningDataFromApi()
    {
        $url = "https://bali.arpro.id/api/getTax";
        $response = Http::timeout(5)->get($url);

        if ($response->successful()) {
            foreach ($response->json() as $rec) {
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
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 100);
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

        // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
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
                'totalCompanies'    // Untuk paginasi chart
            )
        );
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
