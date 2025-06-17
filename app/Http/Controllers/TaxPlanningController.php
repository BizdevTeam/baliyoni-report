<?php

namespace App\Http\Controllers;

use App\Models\TaxPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TaxPlanningController extends Controller
{
    // public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 12);
    //     $search  = $request->input('search');

    //     // ── 1) Fetch & upsert the external API data ────────────────────────────
    //     $url = "https://bali.arpro.id/api/getTax";
    //     $response = Http::timeout(5)->get($url);

    //     if ($response->successful()) {
    //         foreach ($response->json() as $rec) {
    //             // Use the current server date/time for 'tanggal'
    //             $now = Carbon::now()->toDateTimeString();

    //             TaxPlanning::updateOrCreate(
    //                 [
    //                     'nama_perusahaan' => $rec['nama'],
    //                     // If you want to treat each record uniquely by name + date,
    //                     // uncomment the next line instead of the above:
    //                     // 'tanggal'         => $now,
    //                 ],
    //                 [
    //                     'tanggal'         => $now,
    //                     'tax_planning'    => intval($rec['tax_planning']),
    //                     'total_penjualan' => intval($rec['limit_penjualan']),
    //                 ]
    //             );
    //         }
    //     }

    //     // ── 2) Build your base query and apply search ────────────────────────
    //     $baseQuery = TaxPlanning::when($search, fn($q) =>
    //         $q->where('nama_perusahaan', 'like', "%{$search}%")
    //     );

    //     // ── 3) Paginate for the table ────────────────────────────────────────
    //     $rekappenjualans = (clone $baseQuery)->paginate($perPage);

    //     // ── 4) Fetch all data (no pagination) for the chart ─────────────────
    //     $allData = $baseQuery->get();

    //     // ── 5) Group & sum for Chart.js ────────────────────────────────────
    //     $grouped = $allData->groupBy(fn($item) =>
    //         Carbon::parse($item->tanggal)->translatedFormat('F - Y')
    //     );
    //     $months = $grouped->keys()->toArray();

    //     $taxPlanningData    = [];
    //     $totalPenjualanData = [];
    //     foreach ($months as $m) {
    //         $items = $grouped[$m];
    //         $taxPlanningData[]    = $items->sum('tax_planning');
    //         $totalPenjualanData[] = $items->sum('total_penjualan');
    //     }

    //     $chartData = [
    //         'labels'   => $months,
    //         'datasets' => [
    //             [
    //                 'label'           => 'Tax Planning',
    //                 'data'            => $taxPlanningData,
    //                 'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
    //             ],
    //             [
    //                 'label'           => 'Total Penjualan',
    //                 'data'            => $totalPenjualanData,
    //                 'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
    //             ],
    //         ],
    //     ];

    //     // ── 6) Pass exactly the names your Blade expects ─────────────────────
    //     return view(
    //         'accounting.taxplanning',
    //         compact('rekappenjualans', 'perPage', 'search', 'chartData')
    //     );
    // }

//    public function index(Request $request)
//     {
//         $perPage = $request->input('per_page', 12);
//         $search  = $request->input('search');

//         // ── 1) Ambil & perbarui data dari API eksternal ────────────────────────────
//         $url = "https://bali.arpro.id/api/getTax";
//         $response = Http::timeout(5)->get($url);

//         if ($response->successful()) {
//             foreach ($response->json() as $rec) {
//                 // Gunakan tanggal/waktu server saat ini untuk 'tanggal'
//                 $now = Carbon::now()->toDateTimeString();

//                 TaxPlanning::updateOrCreate(
//                     [
//                         'nama_perusahaan' => $rec['nama'],
//                         // Jika Anda ingin memperlakukan setiap catatan secara unik berdasarkan nama + tanggal,
//                         // hapus komentar baris berikutnya sebagai ganti yang di atas:
//                         // 'tanggal'         => $now, // Pertimbangkan untuk menambahkan tanggal jika Anda ingin entri yang benar-benar unik per hari/perusahaan
//                     ],
//                     [
//                         'tanggal'         => $now,
//                         'tax_planning'    => intval($rec['tax_planning']),
//                         'total_penjualan' => intval($rec['limit_penjualan']),
//                     ]
//                 );
//             }
//         }

//         // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
//         $baseQuery = TaxPlanning::when($search, fn($q) =>
//             $q->where('nama_perusahaan', 'like', "%{$search}%")
//         );

//         // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
//         $rekappenjualans = (clone $baseQuery)->paginate($perPage);

//         // ── 4) Ambil semua data (tanpa paginasi) untuk chart ─────────────────
//         $allData = $baseQuery->get();

//         // ── 5) Kelompokkan & jumlahkan untuk Chart.js, digabungkan dalam satu chart ──

//         // Fungsi pembantu untuk menghasilkan warna RGBA acak
//         $generateRandomRgbaColor = function() {
//             $r = rand(0, 255);
//             $g = rand(0, 255);
//             $b = rand(0, 255);
//             return "rgba($r, $g, $b, 0.7)";
//         };

//         $allDatasets = []; // Array untuk menyimpan semua dataset chart
        
//         // Dapatkan semua bulan unik dari data untuk digunakan sebagai label global
//         $allMonths = $allData->map(fn($item) =>
//             Carbon::parse($item->tanggal)->translatedFormat('F - Y')
//         )->unique()->sort(function($a, $b) {
//             // Sortir bulan secara kronologis
//             return Carbon::parse($a)->timestamp <=> Carbon::parse($b)->timestamp;
//         })->values()->toArray();

//         // Kelompokkan data berdasarkan nama perusahaan
//         $groupedByCompany = $allData->groupBy('nama_perusahaan');

//         foreach ($groupedByCompany as $companyName => $companyItems) {
//             // Hasilkan warna acak untuk dataset perusahaan ini
//             $taxPlanningColor = $generateRandomRgbaColor();
//             $totalPenjualanColor = $generateRandomRgbaColor();

//             $taxPlanningData    = [];
//             $totalPenjualanData = [];

//             // Inisialisasi array data dengan 0 untuk semua bulan global
//             foreach ($allMonths as $month) {
//                 $taxPlanningData[$month]    = 0;
//                 $totalPenjualanData[$month] = 0;
//             }

//             // Isi data untuk perusahaan saat ini berdasarkan catatannya
//             $companyItemsGroupedByMonth = $companyItems->groupBy(fn($item) =>
//                 Carbon::parse($item->tanggal)->translatedFormat('F - Y')
//             );

//             foreach ($companyItemsGroupedByMonth as $month => $itemsForMonth) {
//                 $taxPlanningData[$month]    = $itemsForMonth->sum('tax_planning');
//                 $totalPenjualanData[$month] = $itemsForMonth->sum('total_penjualan');
//             }

//             // Tambahkan dataset untuk perusahaan ini ke array allDatasets
//             $allDatasets[] = [
//                 'label'           => 'Tax Planning - ' . $companyName,
//                 'data'            => array_values($taxPlanningData), // Pastikan urutan cocok dengan $allMonths
//                 'backgroundColor' => $taxPlanningColor,
//             ];
//             $allDatasets[] = [
//                 'label'           => 'Total Penjualan - ' . $companyName,
//                 'data'            => array_values($totalPenjualanData), // Pastikan urutan cocok dengan $allMonths
//                 'backgroundColor' => $totalPenjualanColor,
//             ];
//         }

//         $chartData = [
//             'labels'   => $allMonths,
//             'datasets' => $allDatasets,
//         ];

//         // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
//         return view(
//             'accounting.taxplanning',
//             compact('rekappenjualans', 'perPage', 'search', 'chartData')
//         );
//     }

//  public function index(Request $request)
//     {
//         $perPage = $request->input('per_page', 12);
//         $search  = $request->input('search');

//         // ── 1) Ambil & perbarui data dari API eksternal ────────────────────────────
//         $url = "https://bali.arpro.id/api/getTax";
//         $response = Http::timeout(5)->get($url);

//         if ($response->successful()) {
//             foreach ($response->json() as $rec) {
//                 // Gunakan tanggal/waktu server saat ini untuk 'tanggal'
//                 $now = Carbon::now()->toDateTimeString();

//                 TaxPlanning::updateOrCreate(
//                     [
//                         'nama_perusahaan' => $rec['nama'],
//                         // Jika Anda ingin memperlakukan setiap catatan secara unik berdasarkan nama + tanggal,
//                         // hapus komentar baris berikutnya sebagai ganti yang di atas:
//                         // 'tanggal'         => $now, // Pertimbangkan untuk menambahkan tanggal jika Anda ingin entri yang benar-benar unik per hari/perusahaan
//                     ],
//                     [
//                         'tanggal'         => $now,
//                         'tax_planning'    => intval($rec['tax_planning']),
//                         'total_penjualan' => intval($rec['limit_penjualan']),
//                     ]
//                 );
//             }
//         }

//         // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
//         $baseQuery = TaxPlanning::when($search, fn($q) =>
//             $q->where('nama_perusahaan', 'like', "%{$search}%")
//         );

//         // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
//         $rekappenjualans = (clone $baseQuery)->paginate($perPage);

//         // ── 4) Ambil semua data (tanpa paginasi) untuk chart ─────────────────
//         $allData = $baseQuery->get();

//         // ── 5) Kelompokkan & jumlahkan untuk Chart.js, digabungkan dalam satu chart ──

//         // Fungsi pembantu untuk menghasilkan warna RGBA acak
//         $generateRandomRgbaColor = function() {
//             $r = rand(0, 255);
//             $g = rand(0, 255);
//             $b = rand(0, 255);
//             return "rgba($r, $g, $b, 0.7)";
//         };

//         $allDatasets = []; // Array untuk menyimpan semua dataset chart
        
//         // Dapatkan semua bulan unik dari data untuk digunakan sebagai label global
//         $allMonths = $allData->map(fn($item) =>
//             Carbon::parse($item->tanggal)->translatedFormat('F - Y')
//         )->unique()->sort(function($a, $b) {
//             // Sortir bulan secara kronologis
//             return Carbon::parse($a)->timestamp <=> Carbon::parse($b)->timestamp;
//         })->values()->toArray();

//         // Kelompokkan data berdasarkan nama perusahaan
//         $groupedByCompany = $allData->groupBy('nama_perusahaan');

//         // --- Mulai Logika Paginasi Chart ---
//         $chartPerPage = $request->input('chart_per_page', 5); // Default 5 perusahaan per halaman chart
//         // Pastikan chartPerPage adalah kelipatan 5
//         $chartPerPage = max(5, floor($chartPerPage / 5) * 5);


//         $chartPage    = $request->input('chart_page', 1);

//         $companyNames = $groupedByCompany->keys()->toArray();
//         $totalCompanies = count($companyNames);
//         $totalPages = ceil($totalCompanies / $chartPerPage);

//         $offset = ($chartPage - 1) * $chartPerPage;
//         $paginatedCompanyNames = array_slice($companyNames, $offset, $chartPerPage);
//         // --- Akhir Logika Paginasi Chart ---

//         foreach ($paginatedCompanyNames as $companyName) { // Iterasi melalui nama perusahaan yang sudah di-paginasi
//             $companyItems = $groupedByCompany[$companyName]; // Dapatkan item untuk perusahaan saat ini

//             // Hasilkan warna acak untuk dataset perusahaan ini
//             $taxPlanningColor = $generateRandomRgbaColor();
//             $totalPenjualanColor = $generateRandomRgbaColor();

//             $taxPlanningData    = [];
//             $totalPenjualanData = [];

//             // Inisialisasi array data dengan 0 untuk semua bulan global
//             foreach ($allMonths as $month) {
//                 $taxPlanningData[$month]    = 0;
//                 $totalPenjualanData[$month] = 0;
//             }

//             // Isi data untuk perusahaan saat ini berdasarkan catatannya
//             $companyItemsGroupedByMonth = $companyItems->groupBy(fn($item) =>
//                 Carbon::parse($item->tanggal)->translatedFormat('F - Y')
//             );

//             foreach ($companyItemsGroupedByMonth as $month => $itemsForMonth) {
//                 $taxPlanningData[$month]    = $itemsForMonth->sum('tax_planning');
//                 $totalPenjualanData[$month] = $itemsForMonth->sum('total_penjualan');
//             }

//             // Tambahkan dataset untuk perusahaan ini ke array allDatasets
//             $allDatasets[] = [
//                 'label'           => 'Tax Planning - ' . $companyName,
//                 'data'            => array_values($taxPlanningData), // Pastikan urutan cocok dengan $allMonths
//                 'backgroundColor' => $taxPlanningColor,
//             ];
//             $allDatasets[] = [
//                 'label'           => 'Total Penjualan - ' . $companyName,
//                 'data'            => array_values($totalPenjualanData), // Pastikan urutan cocok dengan $allMonths
//                 'backgroundColor' => $totalPenjualanColor,
//             ];
//         }

//         $chartData = [
//             'labels'   => $allMonths,
//             'datasets' => $allDatasets,
//         ];

//         // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
//         return view(
//             'accounting.taxplanning',
//             compact(
//                 'rekappenjualans',
//                 'perPage',
//                 'search',
//                 'chartData',
//                 'chartPage',        // Untuk paginasi chart
//                 'chartPerPage',     // Untuk paginasi chart
//                 'totalPages',       // Untuk paginasi chart
//                 'totalCompanies'    // Untuk paginasi chart
//             )
//         );
    // public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 12);
    //     $search  = $request->input('search');

    //     // ── 1) Ambil & perbarui data dari API eksternal ────────────────────────────
    //     $url = "https://bali.arpro.id/api/getTax";
    //     $response = Http::timeout(5)->get($url);

    //     if ($response->successful()) {
    //         foreach ($response->json() as $rec) {
    //             // Gunakan tanggal/waktu server saat ini untuk 'tanggal'
    //             $now = Carbon::now()->toDateTimeString();

    //             TaxPlanning::updateOrCreate(
    //                 [
    //                     'nama_perusahaan' => $rec['nama'],
    //                     // Jika Anda ingin memperlakukan setiap catatan secara unik berdasarkan nama + tanggal,
    //                     // hapus komentar baris berikutnya sebagai ganti yang di atas:
    //                     // 'tanggal'         => $now, // Pertimbangkan untuk menambahkan tanggal jika Anda ingin entri yang benar-benar unik per hari/perusahaan
    //                 ],
    //                 [
    //                     'tanggal'         => $now,
    //                     'tax_planning'    => intval($rec['tax_planning']),
    //                     'total_penjualan' => intval($rec['limit_penjualan']),
    //                 ]
    //             );
    //         }
    //     }

    //     // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
    //     $baseQuery = TaxPlanning::when($search, fn($q) =>
    //         $q->where('nama_perusahaan', 'like', "%{$search}%")
    //     );

    //     // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
    //     $rekappenjualans = (clone $baseQuery)->paginate($perPage);

    //     // ── 4) Ambil semua data (tanpa paginasi) untuk chart ─────────────────
    //     $allData = $baseQuery->get();

    //     // ── 5) Kelompokkan & jumlahkan untuk Chart.js, digabungkan dalam satu chart ──

    //     // Fungsi pembantu untuk menghasilkan warna RGBA acak
    //     $generateRandomRgbaColor = function() {
    //         $r = rand(0, 255);
    //         $g = rand(0, 255);
    //         $b = rand(0, 255);
    //         return "rgba($r, $g, $b, 0.7)";
    //     };

    //     $allDatasets = []; // Array untuk menyimpan semua dataset chart
        
    //     // Dapatkan semua bulan unik dari data untuk digunakan sebagai label global
    //     $allMonths = $allData->map(fn($item) =>
    //         Carbon::parse($item->tanggal)->translatedFormat('F - Y')
    //     )->unique()->sort(function($a, $b) {
    //         // Sortir bulan secara kronologis
    //         return Carbon::parse($a)->timestamp <=> Carbon::parse($b)->timestamp;
    //     })->values()->toArray();

    //     // Kelompokkan data berdasarkan nama perusahaan
    //     $groupedByCompany = $allData->groupBy('nama_perusahaan');

    //     // --- Mulai Logika Paginasi Chart ---
    //     $chartPerPage = $request->input('chart_per_page', 5); // Default 5 perusahaan per halaman chart
    //     // Pastikan chartPerPage adalah kelipatan 5
    //     $chartPerPage = max(5, floor($chartPerPage / 5) * 5);


    //     $chartPage    = $request->input('chart_page', 1);

    //     $companyNames = $groupedByCompany->keys()->toArray();
    //     $totalCompanies = count($companyNames);
    //     $totalPages = ceil($totalCompanies / $chartPerPage);

    //     $offset = ($chartPage - 1) * $chartPerPage;
    //     $paginatedCompanyNames = array_slice($companyNames, $offset, $chartPerPage);
    //     // --- Akhir Logika Paginasi Chart ---

    //     foreach ($paginatedCompanyNames as $companyName) { // Iterasi melalui nama perusahaan yang sudah di-paginasi
    //         $companyItems = $groupedByCompany[$companyName]; // Dapatkan item untuk perusahaan saat ini

    //         // Hasilkan warna acak untuk dataset perusahaan ini
    //         $taxPlanningColor = $generateRandomRgbaColor();
    //         $totalPenjualanColor = $generateRandomRgbaColor();

    //         $taxPlanningData    = array_fill_keys($allMonths, 0); // Inisialisasi dengan 0 untuk semua bulan global
    //         $totalPenjualanData = array_fill_keys($allMonths, 0); // Inisialisasi dengan 0 untuk semua bulan global

    //         // Isi data untuk perusahaan saat ini
    //         foreach ($companyItems as $item) {
    //             $month = Carbon::parse($item->tanggal)->translatedFormat('F - Y');
    //             // Akumulasikan nilai untuk bulan spesifik
    //             $taxPlanningData[$month]    += $item->tax_planning;
    //             $totalPenjualanData[$month] += $item->total_penjualan;
    //         }

    //         // Tambahkan dataset untuk perusahaan ini ke array allDatasets
    //         $allDatasets[] = [
    //             'label'           => 'Tax Planning - ' . $companyName,
    //             'data'            => array_values($taxPlanningData), // Pastikan urutan cocok dengan $allMonths
    //             'backgroundColor' => $taxPlanningColor,
    //         ];
    //         $allDatasets[] = [
    //             'label'           => 'Total Penjualan - ' . $companyName,
    //             'data'            => array_values($totalPenjualanData), // Pastikan urutan cocok dengan $allMonths
    //             'backgroundColor' => $totalPenjualanColor,
    //         ];
    //     }

    //     $chartData = [
    //         'labels'   => $allMonths,
    //         'datasets' => $allDatasets,
    //     ];

    //     // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
    //     return view(
    //         'accounting.taxplanning',
    //         compact(
    //             'rekappenjualans',
    //             'perPage',
    //             'search',
    //             'chartData',
    //             'chartPage',        // Untuk paginasi chart
    //             'chartPerPage',     // Untuk paginasi chart
    //             'totalPages',       // Untuk paginasi chart
    //             'totalCompanies'    // Untuk paginasi chart
    //         )
    //     );
     public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search  = $request->input('search');

        // ── 1) Ambil & perbarui data dari API eksternal ────────────────────────────
        $url = "https://bali.arpro.id/api/getTax";
        $response = Http::timeout(5)->get($url);

        if ($response->successful()) {
            foreach ($response->json() as $rec) {
                // Gunakan tanggal/waktu server saat ini untuk 'tanggal'
                $now = Carbon::now()->toDateTimeString();

                TaxPlanning::updateOrCreate(
                    [
                        'nama_perusahaan' => $rec['nama'],
                        // Jika Anda ingin memperlakukan setiap catatan secara unik berdasarkan nama + tanggal,
                        // hapus komentar baris berikutnya sebagai ganti yang di atas:
                        // 'tanggal'         => $now, // Pertimbangkan untuk menambahkan tanggal jika Anda ingin entri yang benar-benar unik per hari/perusahaan
                    ],
                    [
                        'tanggal'         => $now,
                        'tax_planning'    => intval($rec['tax_planning']),
                        'total_penjualan' => intval($rec['limit_penjualan']),
                    ]
                );
            }
        }

        // ── 2) Bangun query dasar dan terapkan pencarian ────────────────────────
        $baseQuery = TaxPlanning::when($search, fn($q) =>
            $q->where('nama_perusahaan', 'like', "%{$search}%")
        );

        // ── 3) Paginasikan untuk tabel ────────────────────────────────────────
        $rekappenjualans = (clone $baseQuery)->paginate($perPage);

        // ── 4) Ambil semua data (tanpa paginasi) untuk chart ─────────────────
        $allData = $baseQuery->get();

        // ── 5) Kelompokkan & jumlahkan untuk Chart.js, digabungkan dalam satu chart ──

        // Fungsi pembantu untuk menghasilkan warna RGBA acak
        $generateRandomRgbaColor = function() {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
            return "rgba($r, $g, $b, 0.7)";
        };

        $allDatasets = []; // Array untuk menyimpan semua dataset chart
        
        // Kelompokkan data berdasarkan nama perusahaan
        $groupedByCompany = $allData->groupBy('nama_perusahaan');

        // --- Mulai Logika Paginasi Chart ---
        $chartPerPage = $request->input('chart_per_page', 5); // Default 5 perusahaan per halaman chart
        // Pastikan chartPerPage adalah kelipatan 5
        $chartPerPage = max(5, floor($chartPerPage / 5) * 5);


        $chartPage    = $request->input('chart_page', 1);

        $companyNames = $groupedByCompany->keys()->toArray();
        $totalCompanies = count($companyNames);
        $totalPages = ceil($totalCompanies / $chartPerPage);

        $offset = ($chartPage - 1) * $chartPerPage;
        $paginatedCompanyNames = array_slice($companyNames, $offset, $chartPerPage);
        // --- Akhir Logika Paginasi Chart ---

        $taxPlanningDataForChart = [];
        $totalPenjualanDataForChart = [];

        foreach ($paginatedCompanyNames as $companyName) { // Iterasi melalui nama perusahaan yang sudah di-paginasi
            $companyItems = $groupedByCompany[$companyName]; // Dapatkan item untuk perusahaan saat ini
            
            // Hitung total tax_planning dan total_penjualan untuk perusahaan ini
            $taxPlanningDataForChart[] = $companyItems->sum('tax_planning');
            $totalPenjualanDataForChart[] = $companyItems->sum('total_penjualan');
        }

        // Tambahkan dataset untuk Total Tax Planning
        $allDatasets[] = [
            'label'           => 'Total Tax Planning',
            'data'            => $taxPlanningDataForChart,
            'backgroundColor' => $generateRandomRgbaColor(), // Warna acak untuk dataset ini
        ];
        // Tambahkan dataset untuk Total Penjualan
        $allDatasets[] = [
            'label'           => 'Total Penjualan',
            'data'            => $totalPenjualanDataForChart,
            'backgroundColor' => $generateRandomRgbaColor(), // Warna acak untuk dataset ini
        ];

        $chartData = [
            'labels'   => $paginatedCompanyNames, // Label sekarang adalah nama perusahaan
            'datasets' => $allDatasets,
        ];

        // ── 6) Lewatkan nama yang diharapkan oleh Blade Anda ─────────────────────
        return view(
            'accounting.taxplanning',
            compact(
                'rekappenjualans',
                'perPage',
                'search',
                'chartData',
                'chartPage',        // Untuk paginasi chart
                'chartPerPage',     // Untuk paginasi chart
                'totalPages',       // Untuk paginasi chart
                'totalCompanies'    // Untuk paginasi chart
            )
        );
    }
}
