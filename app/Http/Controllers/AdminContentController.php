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
    // --- CENTRALIZED FILTER PROPERTIES ---
    private $startDate;
    private $endDate;
    private $useFilter = false;

    /**
     * Applies a date filter to a query based on the controller's date properties.
     * It handles date conversion for VARCHAR columns.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tanggalColumn
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDateFilter($query, string $tanggalColumn = 'tanggal')
    {
        if ($this->useFilter) {
            // This assumes your VARCHAR date format is 'YYYY-MM-DD'.
            $dbDriver = DB::connection()->getDriverName();
            $dateConversionSql = ($dbDriver === 'mysql')
                ? "STR_TO_DATE({$tanggalColumn}, '%Y-%m-%d')"
                : "CAST({$tanggalColumn} AS DATE)";

            if ($this->startDate) {
                $query->where(DB::raw($dateConversionSql), '>=', $this->startDate->toDateString());
            }
            if ($this->endDate) {
                $query->where(DB::raw($dateConversionSql), '<=', $this->endDate->toDateString());
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
            // --- REVISED CENTRALIZED FILTER LOGIC ---
            $startDateInput = $request->input('start_date'); // Expects 'YYYY-MM-DD'
            $endDateInput = $request->input('end_date');     // Expects 'YYYY-MM-DD'
            $startMonthInput = $request->input('start_month'); // Expects 'YYYY-MM'
            $endMonthInput = $request->input('end_month');   // Expects 'YYYY-MM'

            if ($startDateInput && $endDateInput) {
                // Priority 1: Handle specific date range from 'start_date' and 'end_date'
                $this->startDate = Carbon::parse($startDateInput)->startOfDay();
                $this->endDate = Carbon::parse($endDateInput)->endOfDay();
                $this->useFilter = true;
            } elseif ($startMonthInput && $endMonthInput) {
                // Priority 2: Handle month range from 'start_month' and 'end_month'
                $this->startDate = Carbon::createFromFormat('Y-m', $startMonthInput)->startOfMonth();
                $this->endDate = Carbon::createFromFormat('Y-m', $endMonthInput)->endOfMonth();
                $this->useFilter = true;
            } else {
                // Default: Last 2 months if no specific filter is provided
                $now = Carbon::now();
                $this->endDate = $now->copy()->endOfMonth();
                $this->startDate = $now->copy()->subMonth()->startOfMonth();
                $this->useFilter = true;
            }
            // --- END FILTER LOGIC ---

            // Marketing
            $dataExportLaporanPenjualan             = $this->safeView(fn() => $this->exportRekapPenjualan());
            $dataExportLaporanPenjualanPerusahaan   = $this->safeView(fn() => $this->exportRekapPenjualanPerusahaan());
            $dataTotalLaporanPenjualanPerusahaan    = $this->safeView(fn() => $this->viewTotalRekapPenjualanPerusahaan());
            $dataExportLaporanPaketAdministrasi     = $this->safeView(fn() => $this->exportLaporanPaketAdministrasi());
            $dataTotalLaporanPaketAdministrasi      = $this->safeView(fn() => $this->ChartTotalLaporanPaketAdministrasi());
            $dataExportStatusPaket                  = $this->safeView(fn() => $this->exportStatusPaket());
            $dataTotalStatusPaket                   = $this->safeView(fn() => $this->ChartTotalStatusPaket());
            $dataExportLaporanPerInstansi           = $this->safeView(fn() => $this->exportLaporanPerInstansi());
            $dataTotalInstansi                      = $this->safeView(fn() => $this->ChartTotalInstansi());

            // Procurement
            $dataExportLaporanHolding               = $this->safeView(fn() => $this->exportLaporanHolding());
            $dataTotalLaporanHolding                = $this->safeView(fn() => $this->ChartTotalHolding());
            $dataComparisonHolding                  = $this->safeView(fn() => $this->ChartComparisonHolding());
            $dataExportLaporanStok                  = $this->safeView(fn() => $this->exportLaporanStok());
            $dataExportLaporanPembelianOutlet       = $this->safeView(fn() => $this->exportLaporanPembelianOutlet());
            $dataExportLaporanNegosiasi             = $this->safeView(fn() => $this->exportLaporanNegosiasi());
            
            // Support
            $dataExportRekapPendapatanASP   = $this->safeView(fn() => $this->exportRekapPendapatanASP());
            $dataTotalRekapPendapatanASP    = $this->safeView(fn() => $this->ChartTotalPendapatanASP());
            $dataExportRekapPiutangASP      = $this->safeView(fn() => $this->exportRekapPiutangASP());
            $dataTotalRekapPiutangASP       = $this->safeView(fn() => $this->ChartTotalPiutangASP());
            $dataLaporanPengiriman          = $this->safeView(fn() => $this->exportLaporanPengiriman());
            
            // HRGA
            $dataPTBOS                      = $this->safeView(fn() => $this->exportPTBOS());
            $dataIJASA                      = $this->safeView(fn() => $this->exportIJASA());
            $dataIJASAGambar                = $this->safeView(fn() => $this->exportIJASAGambar());
            $dataLaporanSakit               = $this->safeView(fn() => $this->exportSakit());
            $dataTotalSakit                 = $this->safeView(fn() => $this->ChartTotalSakit());
            $dataLaporanCuti                = $this->safeView(fn() => $this->exportCuti());
            $dataTotalCuti                  = $this->safeView(fn() => $this->ChartTotalCuti());
            $dataLaporanIzin                = $this->safeView(fn() => $this->exportIzin());
            $dataTotalIzin                  = $this->safeView(fn() => $this->ChartTotalIzin());
            $dataLaporanTerlambat           = $this->safeView(fn() => $this->exportTerlambat());
            $dataTotalTerlambat             = $this->safeView(fn() => $this->ChartTotalTerlambat());
            
            // Accounting
            $dataKHPS                       = $this->safeView(fn() => $this->exportKHPS());
            $dataLabaRugi                   = $this->safeView(fn() => $this->exportLabaRugi());
            $dataNeraca                     = $this->safeView(fn() => $this->exportNeraca());
            $dataRasio                      = $this->safeView(fn() => $this->exportRasio());
            $dataPPn                        = $this->safeView(fn() => $this->exportPPn());
            $dataArusKas                    = $this->safeView(fn() => $this->exportArusKas());
            $dataTaxPlanningReport          = $this->safeView(fn() => $this->exportTaxPlanning());
            
            // SPI
            $dataLaporanSPI                 = $this->safeView(fn() => $this->exportLaporanSPI());
            $dataLaporanSPIIT               = $this->safeView(fn() => $this->exportLaporanSPIIT());
            
            // IT
            $dataTiktok                     = $this->safeView(fn() => $this->exportTiktok());
            $dataInstagram                  = $this->safeView(fn() => $this->exportInstagram());
            $dataBizdev                     = $this->safeView(fn() => $this->exportBizdev());

            return view('components.content', compact(
                'dataExportLaporanPenjualan', 'dataExportLaporanPenjualanPerusahaan', 'dataTotalLaporanPenjualanPerusahaan',
                'dataExportLaporanPaketAdministrasi', 'dataTotalLaporanPaketAdministrasi', 'dataExportStatusPaket',
                'dataTotalStatusPaket', 'dataExportLaporanPerInstansi', 'dataTotalInstansi', 'dataExportLaporanHolding',
                'dataTotalLaporanHolding', 'dataComparisonHolding', 'dataExportLaporanStok', 'dataExportLaporanPembelianOutlet', 'dataExportLaporanNegosiasi',
                'dataExportRekapPendapatanASP', 'dataTotalRekapPendapatanASP', 'dataExportRekapPiutangASP', 'dataTotalRekapPiutangASP',
                'dataLaporanPengiriman', 'dataLaporanSakit', 'dataTotalSakit', 'dataLaporanCuti', 'dataTotalCuti',
                'dataLaporanIzin', 'dataTotalIzin', 'dataLaporanTerlambat', 'dataTotalTerlambat', 'dataKHPS',
                'dataArusKas', 'dataLaporanSPI', 'dataLaporanSPIIT', 'dataLabaRugi', 'dataNeraca', 'dataRasio',
                'dataPPn', 'dataTaxPlanningReport', 'dataTiktok', 'dataInstagram', 'dataBizdev', 'dataPTBOS',
                'dataIJASA', 'dataIJASAGambar'
            ))->with('filtered', $this->useFilter);

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
    // DATA FETCHING FUNCTIONS (Now using centralized filter properties)
    // ===================================================================

    public function exportRekapPenjualan()
    {
        $query = RekapPenjualan::query();
        $this->applyDateFilter($query);

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

    public function exportRekapPenjualanPerusahaan()
    {
        $query = RekapPenjualanPerusahaan::query()
            ->join('perusahaans', 'rekap_penjualan_perusahaans.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, 'rekap_penjualan_perusahaans.tanggal');
        
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

    public function viewTotalRekapPenjualanPerusahaan()
    {
        $query = RekapPenjualanPerusahaan::query()
            ->join('perusahaans', 'rekap_penjualan_perusahaans.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, 'rekap_penjualan_perusahaans.tanggal');

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

    public function exportLaporanPaketAdministrasi()
    {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query);
        
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

    public function ChartTotalLaporanPaketAdministrasi()
    {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query);
        
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

    public function exportStatusPaket()
    {
        $query = StatusPaket::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalStatusPaket()
    {
        $query = StatusPaket::query();
        $this->applyDateFilter($query);
        
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

    public function exportLaporanPerInstansi()
    {
        $query = LaporanPerInstansi::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalInstansi()
    {
        $query = LaporanPerInstansi::query();
        $this->applyDateFilter($query);
        
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

    public function exportLaporanHolding()
    {
        $query = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, 'laporan_holdings.tanggal');

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

    public function ChartTotalHolding()
    {
        $query = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id');
        $this->applyDateFilter($query, 'laporan_holdings.tanggal');

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

    public function ChartComparisonHolding()
    {
        $currentMonth = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();
        $previousMonth = $currentMonth->copy()->subMonthNoOverflow();
        
        $dbDriver = DB::connection()->getDriverName();
        $dateConversionSql = ($dbDriver === 'mysql')
            ? "STR_TO_DATE(laporan_holdings.tanggal, '%Y-%m-%d')"
            : "CAST(laporan_holdings.tanggal AS DATE)";

        $currentMonthData = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id')
            ->where(DB::raw("YEAR({$dateConversionSql})"), '=', $currentMonth->year)
            ->where(DB::raw("MONTH({$dateConversionSql})"), '=', $currentMonth->month)
            ->groupBy('perusahaans.nama_perusahaan')
            ->select('perusahaans.nama_perusahaan', DB::raw('SUM(nilai) as total'))
            ->pluck('total', 'perusahaans.nama_perusahaan');

        $previousMonthData = LaporanHolding::query()
            ->join('perusahaans', 'laporan_holdings.perusahaan_id', '=', 'perusahaans.id')
            ->where(DB::raw("YEAR({$dateConversionSql})"), '=', $previousMonth->year)
            ->where(DB::raw("MONTH({$dateConversionSql})"), '=', $previousMonth->month)
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
    
    public function exportLaporanStok()
    {
        $query = LaporanStok::query();
        $this->applyDateFilter($query);
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

    public function exportLaporanPembelianOutlet()
    {
        $query = LaporanOutlet::query();
        $this->applyDateFilter($query);
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

    public function exportLaporanNegosiasi()
    {
        $query = LaporanNegosiasi::query();
        $this->applyDateFilter($query);
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

    public function exportRekapPendapatanASP()
    {
        $query = RekapPendapatanServisAsp::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalPendapatanASP()
    {
        $query = RekapPendapatanServisAsp::query();
        $this->applyDateFilter($query);
        
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

    public function exportRekapPiutangASP()
    {
        $query = RekapPiutangServisAsp::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalPiutangASP()
    {
        $query = RekapPiutangServisAsp::query();
        $this->applyDateFilter($query);
        
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

    public function exportLaporanPengiriman()
    {
        $query = LaporanDetrans::query();
        $this->applyDateFilter($query);
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
    
    public function exportPTBOS()
    {
        $query = LaporanPtBos::query();
        $this->applyDateFilter($query);
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

    public function exportIJASA()
    {
        $query = LaporanIjasa::query();
        $this->applyDateFilter($query);
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
    
    public function exportIJASAGambar()
    {
        $query = IjasaGambar::query();
        $this->applyDateFilter($query);
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

    public function exportSakit()
    {
        $query = LaporanSakitDivisi::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalSakit()
    {
        $query = LaporanSakitDivisi::query()->select('divisi', DB::raw('SUM(total_sakit) as total_sakit_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_sakit_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Sakit', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportCuti()
    {
        $query = LaporanCutiDivisi::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalCuti()
    {
        $query = LaporanCutiDivisi::query()->select('divisi', DB::raw('SUM(total_cuti) as total_cuti_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_cuti_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Cuti', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportIzin()
    {
        $query = LaporanIzinDivisi::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalIzin()
    {
        $query = LaporanIzinDivisi::query()->select('divisi', DB::raw('SUM(total_izin) as total_izin_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_izin_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Izin', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportTerlambat()
    {
        $query = LaporanTerlambatDivisi::query();
        $this->applyDateFilter($query);
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

    public function ChartTotalTerlambat()
    {
        $query = LaporanTerlambatDivisi::query()->select('divisi', DB::raw('SUM(total_terlambat) as total_terlambat_divisi'))->groupBy('divisi');
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('divisi', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $labels = $rekap->pluck('divisi')->toArray();
        $data = $rekap->pluck('total_terlambat_divisi')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Terlambat', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLabaRugi()
    {
        $query = LaporanLabaRugi::query();
        $this->applyDateFilter($query);
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

    public function exportNeraca()
    {
        $query = LaporanNeraca::query();
        $this->applyDateFilter($query);
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

    public function exportRasio()
    {
        $query = LaporanRasio::query();
        $this->applyDateFilter($query);
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

    public function exportPPn()
    {
        $query = LaporanPpn::query();
        $this->applyDateFilter($query);
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
    
    public function exportTaxPlanning()
    {
        $query = LaporanTaxPlaning::query();
        $this->applyDateFilter($query);
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

    public function exportTiktok()
    {
        $query = ItMultimediaTiktok::query();
        $this->applyDateFilter($query);
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

    public function exportInstagram()
    {
        $query = ItMultimediaInstagram::query();
        $this->applyDateFilter($query);
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

    public function exportBizdev()
    {
        $query = LaporanBizdevGambar::query();
        $this->applyDateFilter($query);
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

    public function exportKHPS()
    {
        $query = KasHutangPiutang::query();
        $this->applyDateFilter($query);
        
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'kas', 'hutang', 'piutang', 'stok')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $totalsQuery = KasHutangPiutang::query();
        $this->applyDateFilter($totalsQuery);
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

    public function exportArusKas()
    {
        $query = ArusKas::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')
                        ->select('tanggal', 'kas_masuk', 'kas_keluar')
                        ->get();

        if ($rekap->isEmpty()) return [];

        $totalsQuery = ArusKas::query();
        $this->applyDateFilter($totalsQuery);
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

    public function exportLaporanSPI()
    {
        $query = LaporanSPI::query();
        $this->applyDateFilter($query);
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

    public function exportLaporanSPIIT()
    {
        $query = LaporanSPITI::query();
        $this->applyDateFilter($query);
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
