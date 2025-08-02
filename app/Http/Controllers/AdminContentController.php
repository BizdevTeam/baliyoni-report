<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\IjasaGambar;
use App\Models\ItMultimediaInstagram;
use App\Models\ItMultimediaTiktok;
use App\Models\KasHutangPiutang;
use App\Models\LaporanBizdevGambar;
use App\Models\LaporanCutiDivisi;
use App\Models\LaporanDetrans;
use App\Models\LaporanHolding;
use App\Models\LaporanIjasa;
use App\Models\LaporanIzinDivisi;
use App\Models\LaporanLabaRugi;
use App\Models\LaporanNegosiasi;
use App\Models\LaporanNeraca;
use App\Models\LaporanOutlet;
use App\Models\LaporanPaketAdministrasi;
use App\Models\LaporanPerInstansi;
use App\Models\LaporanPpn;
use App\Models\LaporanPtBos;
use App\Models\LaporanRasio;
use App\Models\LaporanSakitDivisi;
use App\Models\LaporanSPI;
use App\Models\LaporanSPITI;
use App\Models\LaporanStok;
use App\Models\LaporanTaxPlaning;
use App\Models\LaporanTerlambatDivisi;
use App\Models\Perusahaan;
use App\Models\RekapPendapatanServisAsp;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\RekapPiutangServisAsp;
use App\Models\StatusPaket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AdminContentController extends Controller
{
    /**
     * Applies a date filter to a query based on the request parameters.
     * It handles date conversion for VARCHAR columns.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param string $tanggalColumn
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDateFilter($query, Request $request, string $tanggalColumn = 'tanggal')
    {
        // --- FIXED LOGIC: Convert VARCHAR to DATE for proper filtering ---
        // This assumes your VARCHAR date format is 'YYYY-MM-DD'. 
        // If it's different (e.g., 'DD-MM-YYYY'), change '%Y-%m-%d' to '%d-%m-%Y'.
        $dbDriver = DB::connection()->getDriverName();
        $dateConversionSql = '';

        // Use appropriate conversion function based on the database driver
        if ($dbDriver === 'mysql') {
            $dateConversionSql = "STR_TO_DATE({$tanggalColumn}, '%Y-%m-%d')";
        } else {
            // Fallback for other databases like PostgreSQL, SQLite, SQL Server
            $dateConversionSql = "CAST({$tanggalColumn} AS DATE)";
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('start_date')) {
                try {
                    $query->where(DB::raw($dateConversionSql), '>=', $request->input('start_date'));
                } catch (\Exception $e) {
                    Log::error("Invalid start_date format: " . $request->input('start_date') . ". Error: " . $e->getMessage());
                }
            }

            if ($request->filled('end_date')) {
                try {
                    $query->where(DB::raw($dateConversionSql), '<=', $request->input('end_date'));
                } catch (\Exception $e) {
                    Log::error("Invalid end_date format: " . $request->input('end_date') . ". Error: " . $e->getMessage());
                }
            }
        }
        // Fallback to 'search' input for month-based filtering (YYYY-MM)
        elseif ($request->filled('search') && preg_match('/^\d{4}-\d{2}$/', $request->input('search'))) {
            try {
                $date = Carbon::createFromFormat('Y-m', $request->input('search'));
                // Use database-native functions for YEAR and MONTH on the converted date
                $query->where(DB::raw("YEAR({$dateConversionSql})"), '=', $date->year)
                      ->where(DB::raw("MONTH({$dateConversionSql})"), '=', $date->month);
            } catch (\Exception $e) {
                Log::error("Invalid search date format: " . $request->input('search') . ". Error: " . $e->getMessage());
            }
        }

        return $query;
    }


    /**
     * Utility to generate a random RGBA color string.
     * @return string
     */
    public function getRandomRGBA()
    {
        $opacity = 0.7;
        return sprintf(
            'rgba(%d, %d, %d, %.1f)',
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            $opacity
        );
    }

    /**
     * Wrapper to safely execute data-fetching callbacks and handle potential errors.
     * @param callable $callback
     * @return array
     */
    private function safeView(callable $callback)
    {
        $emptyChart = [
            'labels'   => [],
            'datasets' => [['data' => [], 'backgroundColor' => []]],
        ];

        try {
            $result = $callback();
            if (!is_array($result)) {
                return ['rekap' => [], 'chart' => $emptyChart];
            }
            return [
                'rekap' => $result['rekap'] ?? [],
                'chart' => $result['chart'] ?? $emptyChart,
            ];
        } catch (\Throwable $e) {
            Log::error('Error in safeView: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['rekap' => [], 'chart' => $emptyChart];
        }
    }

    /**
     * Main dashboard/admin content page.
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function adminContent(Request $request)
    {
        try {
            // Marketing
            $dataExportLaporanPenjualan             = $this->safeView(fn() => $this->exportRekapPenjualan($request));
            $dataExportLaporanPenjualanPerusahaan   = $this->safeView(fn() => $this->exportRekapPenjualanPerusahaan($request));
            $dataTotalLaporanPenjualanPerusahaan    = $this->safeView(fn() => $this->viewTotalRekapPenjualanPerusahaan($request));
            $dataExportLaporanPaketAdministrasi     = $this->safeView(fn() => $this->exportLaporanPaketAdministrasi($request));
            $dataTotalLaporanPaketAdministrasi      = $this->safeView(fn() => $this->ChartTotalLaporanPaketAdministrasi($request));
            $dataExportStatusPaket                  = $this->safeView(fn() => $this->exportStatusPaket($request));
            $dataTotalStatusPaket                   = $this->safeView(fn() => $this->ChartTotalStatusPaket($request));
            $dataExportLaporanPerInstansi           = $this->safeView(fn() => $this->exportLaporanPerInstansi($request));
            $dataTotalInstansi                      = $this->safeView(fn() => $this->ChartTotalInstansi($request));

            // Procurement
            $dataExportLaporanHolding               = $this->safeView(fn() => $this->exportLaporanHolding($request));
            $dataTotalLaporanHolding                = $this->safeView(fn() => $this->ChartTotalHolding($request));
            $dataComparisonHolding                  = $this->safeView(fn() => $this->ChartComparisonHolding($request));
            $dataExportLaporanStok                  = $this->safeView(fn() => $this->exportLaporanStok($request));
            $dataExportLaporanPembelianOutlet       = $this->safeView(fn() => $this->exportLaporanPembelianOutlet($request));
            $dataExportLaporanNegosiasi             = $this->safeView(fn() => $this->exportLaporanNegosiasi($request));
            
            // Support
            $dataExportRekapPendapatanASP   = $this->safeView(fn() => $this->exportRekapPendapatanASP($request));
            $dataTotalRekapPendapatanASP    = $this->safeView(fn() => $this->ChartTotalPendapatanASP($request));
            $dataExportRekapPiutangASP      = $this->safeView(fn() => $this->exportRekapPiutangASP($request));
            $dataTotalRekapPiutangASP       = $this->safeView(fn() => $this->ChartTotalPiutangASP($request));
            $dataLaporanPengiriman          = $this->safeView(fn() => $this->exportLaporanPengiriman($request));
            
            // HRGA
            $dataPTBOS                      = $this->safeView(fn() => $this->exportPTBOS($request));
            $dataIJASA                      = $this->safeView(fn() => $this->exportIJASA($request));
            $dataIJASAGambar                = $this->safeView(fn() => $this->exportIJASAGambar($request));
            $dataLaporanSakit               = $this->safeView(fn() => $this->exportSakit($request));
            $dataTotalSakit                 = $this->safeView(fn() => $this->ChartTotalSakit($request));
            $dataLaporanCuti                = $this->safeView(fn() => $this->exportCuti($request));
            $dataTotalCuti                  = $this->safeView(fn() => $this->ChartTotalCuti($request));
            $dataLaporanIzin                = $this->safeView(fn() => $this->exportIzin($request));
            $dataTotalIzin                  = $this->safeView(fn() => $this->ChartTotalIzin($request));
            $dataLaporanTerlambat           = $this->safeView(fn() => $this->exportTerlambat($request));
            $dataTotalTerlambat             = $this->safeView(fn() => $this->ChartTotalTerlambat($request));
            
            // Accounting
            $dataKHPS                       = $this->safeView(fn() => $this->exportKHPS($request));
            $dataLabaRugi                   = $this->safeView(fn() => $this->exportLabaRugi($request));
            $dataNeraca                     = $this->safeView(fn() => $this->exportNeraca($request));
            $dataRasio                      = $this->safeView(fn() => $this->exportRasio($request));
            $dataPPn                        = $this->safeView(fn() => $this->exportPPn($request));
            $dataArusKas                    = $this->safeView(fn() => $this->exportArusKas($request));
            $dataTaxPlanningReport          = $this->safeView(fn() => $this->exportTaxPlanning($request));
            
            // SPI
            $dataLaporanSPI                 = $this->safeView(fn() => $this->exportLaporanSPI($request));
            $dataLaporanSPIIT               = $this->safeView(fn() => $this->exportLaporanSPIIT($request));
            
            // IT
            $dataTiktok                     = $this->safeView(fn() => $this->exportTiktok($request));
            $dataInstagram                  = $this->safeView(fn() => $this->exportInstagram($request));
            $dataBizdev                     = $this->safeView(fn() => $this->exportBizdev($request));


            // Pass all data to the view
            return view('components.content', compact(
                'dataExportLaporanPenjualan',
                'dataExportLaporanPenjualanPerusahaan',
                'dataTotalLaporanPenjualanPerusahaan',
                'dataExportLaporanPaketAdministrasi',
                'dataTotalLaporanPaketAdministrasi',
                'dataExportStatusPaket',
                'dataTotalStatusPaket',
                'dataExportLaporanPerInstansi',
                'dataTotalInstansi',
                'dataExportLaporanHolding',
                'dataTotalLaporanHolding',
                'dataComparisonHolding',
                'dataExportLaporanStok',
                'dataExportLaporanPembelianOutlet',
                'dataExportLaporanNegosiasi',
                'dataExportRekapPendapatanASP',
                'dataTotalRekapPendapatanASP',
                'dataExportRekapPiutangASP',
                'dataTotalRekapPiutangASP',
                'dataLaporanPengiriman',
                'dataPTBOS',
                'dataIJASA',
                'dataIJASAGambar',
                'dataLaporanSakit',
                'dataTotalSakit',
                'dataLaporanCuti',
                'dataTotalCuti',
                'dataLaporanIzin',
                'dataTotalIzin',
                'dataLaporanTerlambat',
                'dataTotalTerlambat',
                'dataKHPS',
                'dataLabaRugi',
                'dataNeraca',
                'dataRasio',
                'dataPPn',
                'dataArusKas',
                'dataTaxPlanningReport',
                'dataLaporanSPI',
                'dataLaporanSPIIT',
                'dataTiktok',
                'dataInstagram',
                'dataBizdev'
            ))->with('filtered', $request->filled('start_date') || $request->filled('end_date') || $request->filled('search'));
        } catch (\Throwable $th) {
            Log::error('Error in adminContent: ' . $th->getMessage());
            return back()->withErrors('An error occurred while loading the dashboard data.');
        }
    }

    public function index(Request $request)
    {
        return $this->adminContent($request);
    }
    
    // ===================================================================
    // DATA FETCHING FUNCTIONS
    // ===================================================================

    public function exportRekapPenjualan(Request $request)
    {
        $query = RekapPenjualan::query();
        $this->applyDateFilter($query, $request);

        $results = $query->orderBy('tanggal', 'asc')
                         ->select('tanggal', 'total_penjualan')
                         ->get();

        if ($results->isEmpty()) return [];

        $formattedData = [];
        $labels = [];
        $data = [];

        foreach ($results as $item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            $formattedData[] = [
                'Tanggal' => $formattedDate,
                'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
            ];
            $labels[] = $formattedDate;
            $data[] = $item->total_penjualan;
        }

        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportRekapPenjualanPerusahaan(Request $request)
    {
        $query = RekapPenjualanPerusahaan::query()
            ->join('perusahaans', 'rekap_penjualan_perusahaans.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, $request, 'rekap_penjualan_perusahaans.tanggal');
        
        $rekap = $query->orderBy('rekap_penjualan_perusahaans.tanggal', 'asc')
                        ->select('rekap_penjualan_perusahaans.tanggal', 'perusahaans.nama_perusahaan', 'rekap_penjualan_perusahaans.total_penjualan')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = [];
        $labels = [];
        $data = [];

        foreach ($rekap as $item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            $formattedData[] = [
                'Tanggal' => $formattedDate,
                'Perusahaan' => $item->nama_perusahaan,
                'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
            ];
            $labels[] = $item->nama_perusahaan . ' - ' . $formattedDate;
            $data[] = $item->total_penjualan;
        }

        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function viewTotalRekapPenjualanPerusahaan(Request $request)
    {
        $query = RekapPenjualanPerusahaan::query()
            ->join('perusahaans', 'rekap_penjualan_perusahaans.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, $request, 'rekap_penjualan_perusahaans.tanggal');

        $akumulasiData = $query->select('perusahaans.nama_perusahaan', DB::raw('SUM(total_penjualan) as total'))
                                ->groupBy('perusahaans.nama_perusahaan')
                                ->pluck('total', 'nama_perusahaan');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanPaketAdministrasi(Request $request)
    {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query, $request);
        
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'website', 'total_paket')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Website' => $item->website,
            'Total Paket' => number_format($item->total_paket, 0, ',', '.'),
        ]);
        
        $labels = $rekap->map(fn($item) => $item->website . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_paket')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Paket', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalLaporanPaketAdministrasi(Request $request)
    {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query, $request);
        
        $akumulasiData = $query->groupBy('website')
                                ->select('website', DB::raw('SUM(total_paket) as total'))
                                ->pluck('total', 'website');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Paket', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportStatusPaket(Request $request)
    {
        $query = StatusPaket::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'status', 'total_paket')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Status' => $item->status,
            'Total Paket' => number_format($item->total_paket, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->status . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_paket')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Paket', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalStatusPaket(Request $request)
    {
        $query = StatusPaket::query();
        $this->applyDateFilter($query, $request);
        
        $akumulasiData = $query->groupBy('status')
                                ->select('status', DB::raw('SUM(total_paket) as total'))
                                ->pluck('total', 'status');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Paket', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanPerInstansi(Request $request)
    {
        $query = LaporanPerInstansi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'instansi', 'nilai')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Instansi' => $item->instansi,
            'Nilai' => 'Rp ' .  number_format($item->nilai, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->instansi . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('nilai')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalInstansi(Request $request)
    {
        $query = LaporanPerInstansi::query();
        $this->applyDateFilter($query, $request);
        
        $akumulasiData = $query->groupBy('instansi')
                                ->select('instansi', DB::raw('SUM(nilai) as total'))
                                ->pluck('total', 'instansi');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanHolding(Request $request)
    {
        $query = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, $request, 'laporan_holdings.tanggal');

        $rekap = $query->orderBy('laporan_holdings.tanggal', 'asc')
                        ->select('laporan_holdings.tanggal', 'perusahaans.nama_perusahaan', 'laporan_holdings.nilai')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Perusahaan' => $item->nama_perusahaan,
            'Nilai' => 'Rp ' .  number_format($item->nilai, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->nama_perusahaan . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('nilai')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalHolding(Request $request)
    {
        $query = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, $request, 'laporan_holdings.tanggal');

        $akumulasiData = $query->groupBy('perusahaans.nama_perusahaan')
                                ->select('perusahaans.nama_perusahaan', DB::raw('SUM(nilai) as total'))
                                ->pluck('total', 'perusahaans.nama_perusahaan');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartComparisonHolding(Request $request)
    {
        $currentMonth = Carbon::now();
        if ($request->filled('search') && preg_match('/^\d{4}-\d{2}$/', $request->input('search'))) {
            $currentMonth = Carbon::createFromFormat('Y-m', $request->input('search'));
        }
        $previousMonth = $currentMonth->copy()->subMonthNoOverflow();

        $currentMonthData = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id')
            ->whereYear('tanggal', $currentMonth->year)
            ->whereMonth('tanggal', $currentMonth->month)
            ->groupBy('perusahaans.nama_perusahaan')
            ->select('perusahaans.nama_perusahaan', DB::raw('SUM(nilai) as total'))
            ->pluck('total', 'perusahaans.nama_perusahaan');

        $previousMonthData = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id')
            ->whereYear('tanggal', $previousMonth->year)
            ->whereMonth('tanggal', $previousMonth->month)
            ->groupBy('perusahaans.nama_perusahaan')
            ->select('perusahaans.nama_perusahaan', DB::raw('SUM(nilai) as total'))
            ->pluck('total', 'perusahaans.nama_perusahaan');
            
        $allCompanyNames = $currentMonthData->keys()->merge($previousMonthData->keys())->unique();

        if ($allCompanyNames->isEmpty()) return [];

        $datasets = [
            [
                'label' => 'Bulan Lalu (' . $previousMonth->translatedFormat('F Y') . ')',
                'data' => $allCompanyNames->map(fn($name) => $previousMonthData->get($name, 0))->values()->toArray(),
                'backgroundColor' => 'rgba(211, 211, 211, 0.9)',
            ],
            [
                'label' => 'Bulan Ini (' . $currentMonth->translatedFormat('F Y') . ')',
                'data' => $allCompanyNames->map(fn($name) => $currentMonthData->get($name, 0))->values()->toArray(),
                'backgroundColor' => 'rgba(220, 20, 60, 0.8)',
            ]
        ];

        return [
            'chart' => [
                'labels' => $allCompanyNames->values()->toArray(),
                'datasets' => $datasets
            ],
        ];
    }
    
    public function exportLaporanStok(Request $request)
    {
        $query = LaporanStok::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'stok')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Stok' => 'Rp ' .  number_format($item->stok, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('stok')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Stok', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanPembelianOutlet(Request $request)
    {
        $query = LaporanOutlet::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'total_pembelian')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Total' => 'Rp ' .  number_format($item->total_pembelian, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_pembelian')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Pembelian', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanNegosiasi(Request $request)
    {
        $query = LaporanNegosiasi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'total_negosiasi')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Total' => 'Rp ' .  number_format($item->total_negosiasi, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_negosiasi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Negosiasi', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportRekapPendapatanASP(Request $request)
    {
        $query = RekapPendapatanServisAsp::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'pelaksana', 'nilai_pendapatan')
                        ->get();

        if ($rekap->isEmpty()) return [];
        
        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
        ];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Pelaksana' => $item->pelaksana,
            'Nilai' => 'Rp ' .  number_format($item->nilai_pendapatan, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->pelaksana . ' (' . 'Rp' . ' ' . number_format($item->nilai_pendapatan) . ')')->toArray();
        $data = $rekap->pluck('nilai_pendapatan')->toArray();
        $backgroundColors = $rekap->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? $this->getRandomRGBA())->toArray();

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Nilai Pendapatan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalPendapatanASP(Request $request)
    {
        $query = RekapPendapatanServisAsp::query();
        $this->applyDateFilter($query, $request);
        
        $akumulasiData = $query->groupBy('pelaksana')
                                ->select('pelaksana', DB::raw('SUM(nilai_pendapatan) as total'))
                                ->pluck('total', 'pelaksana');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
        ];
        $backgroundColors = array_map(fn($label) => $pelaksanaColors[$label] ?? $this->getRandomRGBA(), $labels);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Pendapatan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportRekapPiutangASP(Request $request)
    {
        $query = RekapPiutangServisAsp::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'pelaksana', 'nilai_piutang')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
        ];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Pelaksana' => $item->pelaksana,
            'Nilai' => 'Rp ' .  number_format($item->nilai_piutang, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->pelaksana . ' (' . 'Rp' . ' ' . number_format($item->nilai_piutang) . ')')->toArray();
        $data = $rekap->pluck('nilai_piutang')->toArray();
        $backgroundColors = $rekap->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? $this->getRandomRGBA())->toArray();

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Nilai Piutang', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalPiutangASP(Request $request)
    {
        $query = RekapPiutangServisAsp::query();
        $this->applyDateFilter($query, $request);
        
        $akumulasiData = $query->groupBy('pelaksana')
                                ->select('pelaksana', DB::raw('SUM(nilai_piutang) as total'))
                                ->pluck('total', 'pelaksana');

        if ($akumulasiData->isEmpty()) return [];

        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
        ];
        $backgroundColors = array_map(fn($label) => $pelaksanaColors[$label] ?? $this->getRandomRGBA(), $labels);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Piutang', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanPengiriman(Request $request)
    {
        $query = LaporanDetrans::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'pelaksana', 'total_pengiriman')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Pelaksana' => $item->pelaksana,
            'Total' => 'Rp ' . number_format($item->total_pengiriman, 0, ',', '.'),
        ]);

        $months = $rekap->sortBy('tanggal')->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F - Y'))->unique()->values()->toArray();
        $groupedData = $rekap->groupBy('pelaksana')->map(fn($pelaksanaItems) => $pelaksanaItems->groupBy(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F - Y'))->map->sum('total_pengiriman'));
        
        $colorMap = [
            'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
            'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
        ];

        $datasets = $groupedData->map(function ($monthData, $pelaksana) use ($months, $colorMap) {
            $data = collect($months)->map(fn($month) => $monthData[$month] ?? 0);
            return [
                'label' => $pelaksana,
                'data' => $data->toArray(),
                'backgroundColor' => $colorMap[$pelaksana] ?? $this->getRandomRGBA(),
            ];
        })->values()->toArray();

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $months, 'datasets' => $datasets],
        ];
    }
    
    public function exportPTBOS(Request $request)
    {
        $query = LaporanPtBos::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'pekerjaan', 'kondisi_bulanlalu', 'kondisi_bulanini', 'update', 'rencana_implementasi', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Pekerjaan' => $item->pekerjaan,
            'Kondisi Bulan Lalu' => $item->kondisi_bulanlalu,
            'Kondisi Bulan Ini' => $item->kondisi_bulanini,
            'Update' => $item->update,
            'Rencana Implementasi' => $item->rencana_implementasi,
            'Keterangan' => $item->keterangan
        ])];
    }

    public function exportIJASA(Request $request)
    {
        $query = LaporanIjasa::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'jam', 'permasalahan', 'impact', 'troubleshooting', 'resolve_tanggal', 'resolve_jam')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
            'Jam' => Carbon::parse($item->jam)->translatedFormat('H:i'),
            'Permasalahan' => $item->permasalahan,
            'Impact' => $item->impact,
            'Troubleshooting' => $item->troubleshooting,
            'Resolve Tanggal' => Carbon::parse($item->resolve_tanggal)->translatedFormat('d F Y'),
            'Resolve Jam' => Carbon::parse($item->resolve_jam)->translatedFormat('H:i'),
        ])];
    }
    
    public function exportIJASAGambar(Request $request)
    {
        $query = IjasaGambar::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/hrga/ijasagambar/' . $item->gambar);
            return [
                "Tanggal" => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/hrga/ijasagambar/' . $item->gambar) : asset('images/no-image.png'),
                "Keterangan" => $item->keterangan,
            ];
        })];
    }

    public function exportSakit(Request $request)
    {
        $query = LaporanSakitDivisi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'divisi', 'total_sakit')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Divisi' => $item->divisi,
            'Total' => number_format($item->total_sakit, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->divisi . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_sakit')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Sakit', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalSakit(Request $request)
    {
        $query = LaporanSakitDivisi::query()->select('divisi', DB::raw('SUM(total_sakit) as total_sakit_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_sakit_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Sakit', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportCuti(Request $request)
    {
        $query = LaporanCutiDivisi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'divisi', 'total_cuti')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Divisi' => $item->divisi,
            'Total' => number_format($item->total_cuti, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->divisi . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_cuti')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Cuti', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalCuti(Request $request)
    {
        $query = LaporanCutiDivisi::query()->select('divisi', DB::raw('SUM(total_cuti) as total_cuti_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_cuti_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Cuti', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportIzin(Request $request)
    {
        $query = LaporanIzinDivisi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'divisi', 'total_izin')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Divisi' => $item->divisi,
            'Total' => number_format($item->total_izin, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->divisi . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_izin')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Izin', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalIzin(Request $request)
    {
        $query = LaporanIzinDivisi::query()->select('divisi', DB::raw('SUM(total_izin) as total_izin_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_izin_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Izin', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportTerlambat(Request $request)
    {
        $query = LaporanTerlambatDivisi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'divisi', 'total_terlambat')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Divisi' => $item->divisi,
            'Total' => number_format($item->total_terlambat, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->divisi . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_terlambat')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Terlambat', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalTerlambat(Request $request)
    {
        $query = LaporanTerlambatDivisi::query()->select('divisi', DB::raw('SUM(total_terlambat) as total_terlambat_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_terlambat_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Terlambat', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLabaRugi(Request $request)
    {
        $query = LaporanLabaRugi::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/accounting/labarugi/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/accounting/labarugi/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }

    public function exportNeraca(Request $request)
    {
        $query = LaporanNeraca::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/accounting/neraca/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/accounting/neraca/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }

    public function exportRasio(Request $request)
    {
        $query = LaporanRasio::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/accounting/rasio/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/accounting/rasio/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }

    public function exportPPn(Request $request)
    {
        $query = LaporanPpn::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'thumbnail', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/accounting/ppn/' . $item->thumbnail);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/accounting/ppn/' . $item->thumbnail) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }
    
    public function exportTaxPlanning(Request $request)
    {
        $query = LaporanTaxPlaning::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/accounting/taxplaning/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/accounting/taxplaning/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }

    public function exportTiktok(Request $request)
    {
        $query = ItMultimediaTiktok::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/it/multimediatiktok/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/it/multimediatiktok/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan
            ];
        })];
    }

    public function exportInstagram(Request $request)
    {
        $query = ItMultimediaInstagram::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'keterangan')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/it/multimediainstagram/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/it/multimediainstagram/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan
            ];
        })];
    }

    public function exportBizdev(Request $request)
    {
        $query = LaporanBizdevGambar::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'gambar', 'kendala')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/it/laporanbizdevgambar/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/it/laporanbizdevgambar/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->kendala,
            ];
        })];
    }

    public function exportKHPS(Request $request)
    {
        $query = KasHutangPiutang::query();
        $this->applyDateFilter($query, $request);
        
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'kas', 'hutang', 'piutang', 'stok')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $totalsQuery = KasHutangPiutang::query();
        $this->applyDateFilter($totalsQuery, $request);
        $totals = $totalsQuery->select(
            DB::raw('SUM(kas) as total_kas'),
            DB::raw('SUM(hutang) as total_hutang'),
            DB::raw('SUM(piutang) as total_piutang'),
            DB::raw('SUM(stok) as total_stok')
        )->first();

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Kas' => 'Rp ' .  number_format($item->kas, 0, ',', '.'),
            'Hutang' => 'Rp ' .  number_format($item->hutang, 0, ',', '.'),
            'Piutang' => 'Rp ' .  number_format($item->piutang, 0, ',', '.'),
            'Stok' => 'Rp ' .  number_format($item->stok, 0, ',', '.'),
        ]);

        $labels = [
            "Kas : Rp " . number_format($totals->total_kas, 0, ',', '.'),
            "Hutang : Rp " . number_format($totals->total_hutang, 0, ',', '.'),
            "Piutang : Rp " . number_format($totals->total_piutang, 0, ',', '.'),
            "Stok : Rp " . number_format($totals->total_stok, 0, ',', '.'),
        ];
        $data = [$totals->total_kas, $totals->total_hutang, $totals->total_piutang, $totals->total_stok];
        
        return [
            'rekap' => $formattedData,
            'chart' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'],
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                ]],
            ],
        ];
    }

    public function exportArusKas(Request $request)
    {
        $query = ArusKas::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'kas_masuk', 'kas_keluar')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $totalsQuery = ArusKas::query();
        $this->applyDateFilter($totalsQuery, $request);
        $totals = $totalsQuery->select(
            DB::raw('SUM(kas_masuk) as total_masuk'),
            DB::raw('SUM(kas_keluar) as total_keluar')
        )->first();

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Masuk' => 'Rp ' .  number_format($item->kas_masuk, 0, ',', '.'),
            'Keluar' => 'Rp ' .  number_format($item->kas_keluar, 0, ',', '.'),
        ]);

        $labels = [
            "Kas Masuk : Rp " . number_format($totals->total_masuk, 0, ',', '.'),
            "Kas Keluar : Rp " . number_format($totals->total_keluar, 0, ',', '.'),
        ];
        $data = [$totals->total_masuk, $totals->total_keluar];

        return [
            'rekap' => $formattedData,
            'chart' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => ['#1c64f2', '#ff2323'],
                    'hoverBackgroundColor' => ['#2b6cb0', '#dc2626'],
                ]],
            ],
        ];
    }

    public function exportLaporanSPI(Request $request)
    {
        $query = LaporanSPI::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'aspek', 'masalah', 'solusi', 'implementasi')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Aspek' => $item->aspek,
            'Masalah' => $item->masalah,
            'Solusi' => $item->solusi,
            'Implementasi' => $item->implementasi,
        ])];
    }

    public function exportLaporanSPIIT(Request $request)
    {
        $query = LaporanSPITI::query();
        $this->applyDateFilter($query, $request);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'aspek', 'masalah', 'solusi', 'implementasi')
                        ->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Aspek' => $item->aspek,
            'Masalah' => $item->masalah,
            'Solusi' => $item->solusi,
            'Implementasi' => $item->implementasi,
        ])];
    }
}