<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\IjasaGambar;
use App\Models\ItMultimediaInstagram;
use App\Models\ItMultimediaTiktok;
use App\Models\KasHutangPiutang;
use App\Models\LaporanBizdevGambar;
use App\Models\LaporanCuti;
use App\Models\LaporanDetrans;
use App\Models\LaporanHolding;
use App\Models\LaporanIjasa;
use App\Models\LaporanIzin;
use App\Models\LaporanLabaRugi;
use App\Models\LaporanNegosiasi;
use App\Models\LaporanNeraca;
use App\Models\LaporanOutlet;
use App\Models\LaporanPaketAdministrasi;
use App\Models\LaporanPerInstansi;
use App\Models\LaporanPpn;
use App\Models\LaporanPtBos;
use App\Models\LaporanRasio;
use App\Models\LaporanSakit;
use App\Models\LaporanSPI;
use App\Models\LaporanSPITI;
use App\Models\LaporanStok;
use App\Models\LaporanTaxPlaning;
use App\Models\LaporanTerlambat;
use App\Models\RekapPendapatanServisASP;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\RekapPiutangServisASP;
use App\Models\StatusPaket;
use App\Models\TaxPlanning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


class AdminContentController extends Controller
{
    private $month;
    private $year;
    private $startDate;
    private $endDate;
    private $useFilter = false;

    public function __construct($startMonth = null, $endMonth = null)
    {
        if ($startMonth && $endMonth) {
            $this->startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $this->endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
            $this->useFilter = true;
        } elseif ($startMonth) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $startMonth);
            $this->month = $date->month;
            $this->year = $date->year;
            $this->useFilter = true;
        } elseif ($endMonth) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $endMonth);
            $this->month = $date->month;
            $this->year = $date->year;
            $this->useFilter = true;
        } else {
            $date = \Carbon\Carbon::now();
            $this->month = $date->month;
            $this->year = $date->year;
            $this->useFilter = false;
        }
    }

    // // Fungsi untuk menerapkan filter pada query jika diperlukan
    // private function applyDateFilter($query, $tanggalColumn = 'tanggal')
    // {
    //     // Hanya terapkan filter jika flag useFilter aktif
    //     if (!$this->useFilter) {
    //         return $query; // Return query tanpa filter
    //     }

    //     if (isset($this->startDate) && isset($this->endDate)) {
    //         // Kedua bulan diisi: filter rentang tanggal
    //         $query->whereBetween($tanggalColumn, [$this->startDate, $this->endDate]);
    //     } elseif (isset($this->month) && isset($this->year)) {
    //         // Hanya satu bulan diisi: filter satu bulan dengan LIKE
    //         $search = sprintf('%04d-%02d', $this->year, $this->month);
    //         $query->whereRaw("DATE_FORMAT($tanggalColumn, '%Y-%m') LIKE ?", ["%$search%"]);
    //     }

    //     return $query;
    // }

    private function applyDateFilter($query, $tanggalColumn = 'tanggal')
    {
        if (!$this->useFilter) {
            return $query;
        }
    
        if (isset($this->startDate) && isset($this->endDate)) {
            // Filter rentang tanggal, konversi varchar ke DATE
            $query->whereBetween(
                DB::raw("STR_TO_DATE($tanggalColumn, '%Y-%m-%d')"),
                [$this->startDate, $this->endDate]
            );
        } elseif (isset($this->month) && isset($this->year)) {
            // Filter berdasarkan bulan dan tahun
            $search = sprintf('%04d-%02d', $this->year, $this->month);
            $query->whereRaw("DATE_FORMAT(STR_TO_DATE($tanggalColumn, '%Y-%m-%d'), '%Y-%m') = ?", [$search]);
        }
    
        return $query;
    }
    
    // Fungsi generate warna random
    public function getRandomRGBA()
    {
        $opacity = 0.7; // Opacity value between 0 and 1
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    private function safeView(callable $callback)
    {
        $emptyChart = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => [],
                    'backgroundColor' => [],
                ]
            ]
        ];

        try {
            $result = $callback();
            return is_array($result) ? $result : ['rekap' => [], 'chart' => $emptyChart];
        } catch (\Throwable $e) {
            return ['rekap' => [], 'chart' => $emptyChart];
        }
    }

    public function adminContent(Request $request)
    {
        try {
            $shouldFilter = $request->has('filter') ? $request->input('filter') : false;
            $this->useFilter = filter_var($shouldFilter, FILTER_VALIDATE_BOOLEAN);
            // Helper function untuk default chart kosong
            $emptyChart = [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => [],
                        'backgroundColor' => [],
                    ]
                ]
            ];

            // === Untuk divisi Marketing ===
            $dataExportLaporanPenjualan = $this->safeView(fn() => $this->exportRekapPenjualan($request));
            $dataExportLaporanPenjualanPerusahaan = $this->safeView(fn() => $this->exportRekapPenjualanPerusahaan($request));
            $dataTotalLaporanPenjualanPerusahaan = $this->safeView(fn() => $this->viewTotalRekapPenjualanPerusahaan($request));
            $dataExportLaporanPaketAdministrasi = $this->safeView(fn() => $this->exportLaporanPaketAdministrasi($request));
            $dataTotalLaporanPaketAdministrasi = $this->safeView(fn() => $this->ChartTotalLaporanPaketAdministrasi($request));
            $dataExportStatusPaket = $this->safeView(fn() => $this->exportStatusPaket($request));
            $dataTotalStatusPaket = $this->safeView(fn() => $this->ChartTotalStatusPaket($request));
            $dataExportLaporanPerInstansi = $this->safeView(fn() => $this->exportLaporanPerInstansi($request));
            $dataTotalInstansi = $this->safeView(fn() => $this->ChartTotalInstansi($request));

            // === Untuk divisi Procurement ===
            $dataExportLaporanHolding = $this->safeView(fn() => $this->exportLaporanHolding($request));
            $dataTotalLaporanHolding = $this->safeView(fn() => $this->ChartTotalHolding($request));
            $dataExportLaporanStok = $this->safeView(fn() => $this->exportLaporanStok($request));
            $dataExportLaporanPembelianOutlet = $this->safeView(fn() => $this->exportLaporanPembelianOutlet($request));
            $dataExportLaporanNegosiasi = $this->safeView(fn() => $this->exportLaporanNegosiasi($request));

            // === Untuk divisi Supports ===
            $dataExportRekapPendapatanASP = $this->safeView(fn() => $this->exportRekapPendapatanASP($request));
            $dataTotalRekapPendapatanASP = $this->safeView(fn() => $this->ChartTotalPendapatanASP($request));
            $dataExportRekapPiutangASP = $this->safeView(fn() => $this->exportRekapPiutangASP($request));
            $dataTotalRekapPiutangASP = $this->safeView(fn() => $this->ChartTotalPiutangASP($request));
            $dataLaporanPengiriman = $this->safeView(fn() => $this->exportLaporanPengiriman($request));

            // === Untuk divisi HRGA ===
            $dataPTBOS = $this->safeView(fn() => $this->exportPTBOS($request));
            $dataIJASA = $this->safeView(fn() => $this->exportIJASA($request));
            $dataIJASAGambar = $this->safeView(fn() => $this->exportIJASAGambar($request));
            $dataLaporanSakit = $this->safeView(fn() => $this->exportSakit($request));
            $dataTotalSakit = $this->safeView(fn() => $this->ChartTotalSakit($request));
            $dataLaporanCuti = $this->safeView(fn() => $this->exportCuti($request));
            $dataTotalCuti = $this->safeView(fn() => $this->ChartTotalCuti($request));
            $dataLaporanIzin = $this->safeView(fn() => $this->exportIzin($request));
            $dataTotalIzin = $this->safeView(fn() => $this->ChartTotalIzin($request));
            $dataLaporanTerlambat = $this->safeView(fn() => $this->exportTerlambat($request));
            $dataTotalTerlambat = $this->safeView(fn() => $this->ChartTotalTerlambat($request));

            // === Untuk divisi Accounting ===
            $dataKHPS = $this->safeView(fn() => $this->exportKHPS($request));
            $dataLabaRugi = $this->safeView(fn() => $this->exportLabaRugi($request));
            $dataNeraca = $this->safeView(fn() => $this->exportNeraca($request));
            $dataRasio = $this->safeView(fn() => $this->exportRasio($request));
            $dataPPn = $this->safeView(fn() => $this->exportPPn($request));
            $dataArusKas = $this->safeView(fn() => $this->exportArusKas($request));
            $dataTaxPlanningReport = $this->safeView(fn() => $this->exportTaxPlanning($request));

            // === Untuk divisi SPI ===
            $dataLaporanSPI = $this->safeView(fn() => $this->exportLaporanSPI($request));
            $dataLaporanSPIIT = $this->safeView(fn() => $this->exportLaporanSPIIT($request));

            // IT
            $dataTiktok = $this->safeView(fn() => $this->exportTiktok($request));
            $dataInstagram = $this->safeView(fn() => $this->exportInstagram($request));
            $dataBizdev = $this->safeView(fn() => $this->exportBizdev($request));


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
                'dataExportLaporanStok',
                'dataExportLaporanPembelianOutlet',
                'dataExportLaporanNegosiasi',
                'dataExportRekapPendapatanASP',
                'dataTotalRekapPendapatanASP',
                'dataExportRekapPiutangASP',
                'dataTotalRekapPiutangASP',
                'dataLaporanPengiriman',
                'dataLaporanSakit',
                'dataTotalSakit',
                'dataLaporanCuti',
                'dataTotalCuti',
                'dataLaporanIzin',
                'dataTotalIzin',
                'dataLaporanTerlambat',
                'dataTotalTerlambat',
                'dataKHPS',
                'dataArusKas',
                'dataLaporanSPI',
                'dataLaporanSPIIT',
                'dataArusKas',
                'dataLabaRugi',
                'dataNeraca',
                'dataRasio',
                'dataPPn',
                'dataTaxPlanningReport',
                'dataTiktok',
                'dataInstagram',
                'dataBizdev',
                'dataPTBOS',
                'dataIJASA',
                'dataIJASAGambar',

            ))
                ->with('month', $this->month)
                ->with('year', $this->year)
                ->with('filtered', $this->useFilter);
        } catch (\Throwable $th) {
            Log::error('Error exporting all laporan (func adminContent): ' . $th->getMessage());
            return back()->withErrors($th->getMessage());
        }
    }

    public function index(Request $request)
    {
        return $this->adminContent($request);
    }

    public function exportRekapPenjualan(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = RekapPenjualan::query();

            // Apply filter only if needed
            $query = $instance->applyDateFilter($query);

            $rekapPenjualan = $query->orderBy('tanggal', 'asc')->get();
            // dd($instance->startDate ?? null, $instance->endDate ?? null); // Untuk debug

            if ($rekapPenjualan->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapPenjualan->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
                ];
            });

            $labels = $rekapPenjualan->map(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            })->toArray();

            $data = $rekapPenjualan->pluck('total_penjualan')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportRekapPenjualan): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }


    public function exportRekapPenjualanPerusahaan(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = RekapPenjualanPerusahaan::query();
            $query = $instance->applyDateFilter($query);

            $rekapPenjualanPerusahaan = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPenjualanPerusahaan->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapPenjualanPerusahaan->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Perusahaan' => $item->perusahaan->nama_perusahaan,
                    'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapPenjualanPerusahaan->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
            })->toArray();

            $data = $rekapPenjualanPerusahaan->pluck('total_penjualan')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportRekapPenjualanPerusahaan): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Chart total rekap penjualan perusahaan 
    public function viewTotalRekapPenjualanPerusahaan(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = RekapPenjualanPerusahaan::query();
            $query = $instance->applyDateFilter($query);

            $rekapPenjualanPerusahaan = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPenjualanPerusahaan->isEmpty()) {
                return 'Data tidak ditemukan.';
            }
            // Akumulasi total penjualan berdasarkan nama perusahaan
            $akumulasiData = [];
            foreach ($rekapPenjualanPerusahaan as $item) {
                $namaPerusahaan = $item->perusahaan->nama_perusahaan;
                if (!isset($akumulasiData[$namaPerusahaan])) {
                    $akumulasiData[$namaPerusahaan] = 0;
                }
                $akumulasiData[$namaPerusahaan] += $item->total_penjualan;
            }
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func viewTotalRekapPenjualanPerusahaan): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPaketAdministrasi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = LaporanPaketAdministrasi::query();
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPaketAdministrasi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPaketAdministrasi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }
            $formattedData = $rekapLaporanPaketAdministrasi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Website' => $item->website,
                    'Total Paket' => number_format($item->total_paket, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapLaporanPaketAdministrasi->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->website . ' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanPaketAdministrasi->pluck('total_paket')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (exportLaporanPaketAdministrasi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalLaporanPaketAdministrasi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanPaketAdministrasi::query();
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPaketAdministrasi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPaketAdministrasi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Akumulasi total penjualan berdasarkan nama website
            $akumulasiData = [];
            foreach ($rekapLaporanPaketAdministrasi as $item) {
                $namaWebsite = $item->website;
                if (!isset($akumulasiData[$namaWebsite])) {
                    $akumulasiData[$namaWebsite] = 0;
                }
                $akumulasiData[$namaWebsite] += $item->total_paket;
            }

            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalLaporanPaketAdministrasi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportStatusPaket(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = StatusPaket::query();
            $query = $instance->applyDateFilter($query);

            $rekapStatusPaket = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapStatusPaket->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapStatusPaket->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Status' => $item->status,
                    'Total Paket' => number_format($item->total_paket, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapStatusPaket->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->status . ' - ' . $formattedDate;
            })->toArray();

            $data = $rekapStatusPaket->pluck('total_paket')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportStatusPaket): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalStatusPaket(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = StatusPaket::query();
            $query = $instance->applyDateFilter($query);

            $rekapStatusPaket = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapStatusPaket->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $akumulasiData = [];
            foreach ($rekapStatusPaket as $item) {
                $namaStatus = $item->status;
                if (!isset($akumulasiData[$namaStatus])) {
                    $akumulasiData[$namaStatus] = 0;
                } 
                $akumulasiData[$namaStatus] += $item->total_paket;
            }

            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalStatusPaket): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPerInstansi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanPerInstansi::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPerInstansi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPerInstansi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapLaporanPerInstansi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Instansi' => $item->instansi,
                    'Nilai' => 'Rp ' .  number_format($item->nilai, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapLaporanPerInstansi->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->instansi . ' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanPerInstansi->pluck('nilai')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanPerInstansi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalInstansi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanPerInstansi::query();
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPerInstansi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPerInstansi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $akumulasiData = [];
            foreach ($rekapLaporanPerInstansi as $item) {
                $namaInstansi = $item->instansi;
                if (!isset($akumulasiData[$namaInstansi])) {
                    $akumulasiData[$namaInstansi] = 0;
                } 
                $akumulasiData[$namaInstansi] += $item->nilai;
            }
            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalInstansi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Procurement
    public function exportLaporanHolding(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanHolding::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapLaporanHolding = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanHolding->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapLaporanHolding->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Perusahaan' => $item->perusahaan->nama_perusahaan,
                    'Nilai' => 'Rp ' .  number_format($item->nilai, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapLaporanHolding->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanHolding->pluck('nilai')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanHolding): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalHolding(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanHolding::query();
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPerInstansi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPerInstansi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $akumulasiData = [];
            foreach ($rekapLaporanPerInstansi as $item) {
                $namaPerusahaan = $item->perusahaan->nama_perusahaan;
                if (!isset($akumulasiData[$namaPerusahaan])) {
                    $akumulasiData[$namaPerusahaan] = 0;
                }
                $akumulasiData[$namaPerusahaan] += $item->nilai;
            }
                    // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalHolding): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanStok(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanStok::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);


            $rekapLaporanStok = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanStok->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapLaporanStok->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Stok' => 'Rp ' .  number_format($item->stok, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapLaporanStok->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $formattedDate;
            })->toArray();

            $data = $rekapLaporanStok->pluck('stok')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanStok): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPembelianOutlet(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanOutlet::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapLaporanPembelianOutlet = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanPembelianOutlet->isEmpty()) {
                return 'Data tidak ditemukan.';
            }
            $formattedData = $rekapLaporanPembelianOutlet->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Total' => 'Rp ' .  number_format($item->total_pembelian, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapLaporanPembelianOutlet->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $formattedDate;
            })->toArray();

            $data = $rekapLaporanPembelianOutlet->pluck('total_pembelian')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanPembelianOutlet): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanNegosiasi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanNegosiasi::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = $instance->applyDateFilter($query);

            $rekapLaporanNegosiasi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLaporanNegosiasi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapLaporanNegosiasi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Total' => 'Rp ' .  number_format($item->total_negosiasi, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapLaporanNegosiasi->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $formattedDate;
            })->toArray();

            $data = $rekapLaporanNegosiasi->pluck('total_negosiasi')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanNegosiasi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Supports
    public function exportRekapPendapatanASP(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = RekapPendapatanServisASP::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapPendapatanASP = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPendapatanASP->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            $pelaksanaColors = [
                'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
                'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
                'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
                'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
                'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
            ];

            $formattedData = $rekapPendapatanASP->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Pelaksana' => $item->pelaksana,
                    'Nilai' => 'Rp ' .  number_format($item->nilai_pendapatan, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapPendapatanASP->map(function ($item) {
                return $item->pelaksana . ' (' . 'Rp' . ' ' . number_format($item->nilai_pendapatan) . ')';
            })->toArray();
            $data = $rekapPendapatanASP->pluck('nilai_pendapatan')->toArray(); // Nilai pendapatan


            $backgroundColors = $rekapPendapatanASP->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportRekapPendapatanASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalPendapatanASP(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = RekapPendapatanServisAsp::query();
            $query = $instance->applyDateFilter($query);

            $rekapPendapatanASP = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPendapatanASP->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Akumulasi total piutang berdasarkan pelaksana
            $akumulasiData = $rekapPendapatanASP->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_pendapatan'));

            // Warna berdasarkan nama pelaksana
            $pelaksanaColors = [
                'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
                'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
                'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
                'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
                'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
            ];

            // Siapkan data untuk chart
            $labels = $akumulasiData->keys()->toArray();
            $data = $akumulasiData->values()->toArray();
            $backgroundColors = array_map(fn($label) => $pelaksanaColors[$label] ?? 'rgba(0, 0, 0, 0.7)', $labels);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Piutang',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalPendapatanASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportRekapPiutangASP(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = RekapPiutangServisASP::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapPiutangServisASP = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPiutangServisASP->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            $pelaksanaColors = [
                'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
                'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
                'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
                'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
                'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
            ];

            $formattedData = $rekapPiutangServisASP->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Pelaksana' => $item->pelaksana,
                    'Nilai' => 'Rp ' .  number_format($item->nilai_piutang, 0, ',', '.'),
                ];
            });
            // Siapkan data untuk chart
            $labels = $rekapPiutangServisASP->map(function ($item) {
                return $item->pelaksana . ' (' . 'Rp' . ' ' . number_format($item->nilai_piutang) . ')';
            })->toArray();
            $data = $rekapPiutangServisASP->pluck('nilai_piutang')->toArray(); // Nilai pendapatan


            $backgroundColors = $rekapPiutangServisASP->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportRekapPiutangASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalPiutangASP(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = RekapPiutangServisAsp::query();
            $query = $instance->applyDateFilter($query);

            $rekapPiutangServisASP = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPiutangServisASP->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Akumulasi total piutang berdasarkan pelaksana
            $akumulasiData = $rekapPiutangServisASP->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_pendapatan'));

            // Warna berdasarkan nama pelaksana
            $pelaksanaColors = [
                'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
                'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
                'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
                'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
                'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
            ];

            // Siapkan data untuk chart
            $labels = $akumulasiData->keys()->toArray();
            $data = $akumulasiData->values()->toArray();
            $backgroundColors = array_map(fn($label) => $pelaksanaColors[$label] ?? 'rgba(0, 0, 0, 0.7)', $labels);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Piutang',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalPiutangASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPengiriman(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanDetrans::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapPengiriman = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPengiriman->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data untuk tabel
            $formattedData = $rekapPengiriman->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Pelaksana' => $item->pelaksana,
                    'Total' => 'Rp ' . number_format($item->total_pengiriman, 0, ',', '.'),
                ];
            });

            // Ambil semua bulan unik yang muncul di data
            $months = $rekapPengiriman
                ->sortBy('tanggal')
                ->map(fn($item) => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y'))
                ->unique()
                ->values()
                ->toArray();

            // Group data berdasarkan pelaksana dan bulan
            $groupedData = [];
            foreach ($rekapPengiriman as $item) {
                $bulan = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
                $groupedData[$item->pelaksana][$bulan][] = $item->total_pengiriman;
            }

            // Warna khusus per pelaksana
            $colorMap = [
                'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
                'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
            ];
            $defaultColor = 'rgba(128, 128, 128, 0.7)';

            // Siapkan datasets chart
            $datasets = collect($groupedData)->map(function ($monthData, $pelaksana) use ($months, $colorMap, $defaultColor) {
                $data = collect($months)->map(function ($month) use ($monthData) {
                    return isset($monthData[$month]) ? array_sum($monthData[$month]) : 0;
                });

                return [
                    'label' => $pelaksana,
                    'data' => $data->toArray(),
                    'backgroundColor' => $colorMap[$pelaksana] ?? $defaultColor,
                ];
            })->values()->toArray();

            $chartData = [
                'labels' => $months,
                'datasets' => $datasets,
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLaporanPengiriman): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi HRGA
    public function exportPTBOS(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanPtBos::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = $instance->applyDateFilter($query);

            $rekapPTBOS = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPTBOS->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            $formattedData =  $rekapPTBOS->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Pekerjaan' => $item->pekerjaan,
                    'Kondisi Bulan Lalu' => $item->kondisi_bulanlalu,
                    'Kondisi Bulan Ini' => $item->kondisi_bulanini,
                    'Update' => $item->update,
                    'Rencana Implementasi' => $item->rencana_implementasi,
                    'Keterangan' => $item->keterangan
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportPTBOS): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi HRGA
    public function exportIJASA(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanIjasa::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = $instance->applyDateFilter($query);
            $rekapIJASA = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapIJASA->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData =  $rekapIJASA->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                    'Jam' => \Carbon\Carbon::parse($item->jam)->translatedFormat('H:i'),
                    'Permasalahan' => $item->permasalahan,
                    'Impact' => $item->impact,
                    'Troubleshooting' => $item->troubleshooting,
                    'Resolve Tanggal' => \Carbon\Carbon::parse($item->resolve_tanggal)->translatedFormat('d F Y'),
                    'Resolve Jam' => \Carbon\Carbon::parse($item->resolve_jam)->translatedFormat('H:i'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportIJASA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi tiktok
    public function exportIJASAGambar(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = IjasaGambar::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $ijasaGambar = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($ijasaGambar->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            // Format data dengan path gambar
            $formattedData = $ijasaGambar->map(function ($item) {
                $imagePath = public_path('images/hrga/ijasagambar/' . $item->gambar);
                return [
                    "Tanggal" => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/hrga/ijasagambar/' . $item->gambar)
                        : asset('images/no-image.png'),
                    "Keterangan" => $item->keterangan,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportIJASAGambar): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi HRGA
    public function exportSakit(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanSakit::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapSakit = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapSakit->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData =  $rekapSakit->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Nama' => $item->nama,
                    'Total' => number_format($item->total_sakit, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapSakit->map(function($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->nama. ' - ' .$formattedDate;
            })->toArray();
            $data = $rekapSakit->pluck('total_sakit')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Cuti',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportSakit): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalSakit(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanSakit::query();
            $query = $instance->applyDateFilter($query);

            $rekapSakit = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapSakit->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $akumulasiData = [];
            foreach ($rekapSakit as $item) {
                $namaKey = $item->nama;

                if (!isset($akumulasiData[$namaKey])) {
                    $akumulasiData[$namaKey] = 0;
                }
                $akumulasiData[$namaKey] += $item->total_sakit;
            }
        
            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Sakit per Bulan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalSakit): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi HRGA
    public function exportCuti(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanCuti::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapCuti = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapCuti->isEmpty()) {
                return 'Data tidak ditemukan.';
            }
            $formattedData =  $rekapCuti->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Nama' => $item->nama,
                    'Total' => number_format($item->total_cuti, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapCuti->map(function($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->nama. ' - ' .$formattedDate;
            })->toArray();

            $data = $rekapCuti->pluck('total_cuti')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Cuti',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportCuti): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalCuti(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanCuti::query();
            $query = $instance->applyDateFilter($query);

            $rekapCuti = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapCuti->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $akumulasiData = [];
            foreach ($rekapCuti as $item) {
                $namaKey = $item->nama;

                if (!isset($akumulasiData[$namaKey])) {
                    $akumulasiData[$namaKey] = 0;
                }
                $akumulasiData[$namaKey] += $item->total_cuti;
            }        

            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Cuti per Bulan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalCuti): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi HRGA
    public function exportIzin(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanIzin::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapIzin = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapIzin->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            $formattedData =  $rekapIzin->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Nama' => $item->nama,
                    'Total' => number_format($item->total_izin, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapIzin->map(function($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->nama. ' - ' .$formattedDate;
            })->toArray();

            $data = $rekapIzin->pluck('total_izin')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Cuti',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportIzin): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalIzin(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanIzin::query();
            $query = $instance->applyDateFilter($query);

            $rekapIzin = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapIzin->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Akumulasi total_sakit berdasarkan bulan
            $akumulasiData = [];
            foreach ($rekapIzin as $item) {
                $namaKey = $item->nama;

                if (!isset($akumulasiData[$namaKey])) {
                    $akumulasiData[$namaKey] = 0;
                }
                $akumulasiData[$namaKey] += $item->total_izin;
            }    

            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Izin per Bulan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalIzin): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi HRGA
    public function exportTerlambat(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanTerlambat::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapTerlambat = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapTerlambat->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            $formattedData =  $rekapTerlambat->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Nama' => $item->nama,
                    'Total' => number_format($item->total_terlambat, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
            $labels = $rekapTerlambat->map(function($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->nama. ' - ' .$formattedDate;
            })->toArray();
            $data = $rekapTerlambat->pluck('total_terlambat')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Cuti',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportTerlambat): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function ChartTotalTerlambat(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = LaporanTerlambat::query();
            $query = $instance->applyDateFilter($query);

            $rekapTerlambat = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapTerlambat->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Akumulasi total_terlambat berdasarkan nama dan bulan
            $akumulasiData = [];
            foreach ($rekapTerlambat as $item) {
                $namaKey = $item->nama;

                if (!isset($akumulasiData[$namaKey])) {
                    $akumulasiData[$namaKey] = 0;
                }
                $akumulasiData[$namaKey] += $item->total_terlambat;
            }

            // Siapkan data untuk chart
            $labels = array_keys($akumulasiData);
            $data = array_values($akumulasiData);
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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
            return [
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ChartTotalTerlambat): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportLabaRugi(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanLabaRugi::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }

            $query = $instance->applyDateFilter($query);

            $rekapLabaRugi = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapLabaRugi->isEmpty()) {
                return 'Data tidak ditemukan.';
            }


            // Format data dengan path gambar
            $formattedData = $rekapLabaRugi->map(function ($item) {
                $imagePath = public_path('images/accounting/labarugi/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/labarugi/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan,

                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportLabaRugi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportNeraca(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanNeraca::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapNeraca = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapNeraca->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapNeraca->map(function ($item) {
                $imagePath = public_path('images/accounting/neraca/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/neraca/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan,

                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportNeraca): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportRasio(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanRasio::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapRasio = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapRasio->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapRasio->map(function ($item) {
                $imagePath = public_path('images/accounting/rasio/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/rasio/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan,

                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportRasio): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportPPn(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanPpn::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapPPn = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapPPn->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapPPn->map(function ($item) {
                $imagePath = public_path('images/accounting/ppn/' . $item->thumbnail);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->thumbnail) && file_exists($imagePath))
                        ? asset('images/accounting/ppn/' . $item->thumbnail)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportPPn): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi taxplanning
    // public function exportTaxPlanning(Request $request)
    // {
    //     try {
    //         $startMonth = $request->input('start_month'); // format Y-m
    //         $endMonth = $request->input('end_month');     // format Y-m

    //         $instance = new self($startMonth, $endMonth);

    //         // Bangun query berdasarkan data constructor
    //         $query = LaporanTaxPlaning::query();

    //         if ($request->has('filter')) {
    //             $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
    //         }
    //         $query = $instance->applyDateFilter($query);

    //         $rekapTaxPlanning = $query
    //             ->orderBy('tanggal', 'asc')
    //             ->get();

    //         if ($rekapTaxPlanning->isEmpty()) {
    //             return 'Data tidak ditemukan.';
    //         }

    //         // Format data dengan path gambar
    //         $formattedData = $rekapTaxPlanning->map(function ($item) {
    //             $imagePath = public_path('images/accounting/taxplaning/' . $item->gambar);
    //             return [
    //                 'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
    //                 'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
    //                     ? asset('images/accounting/taxplaning/' . $item->gambar)
    //                     : asset('images/no-image.png'),
    //                 'Keterangan' => $item->keterangan,
    //             ];
    //         });

    //         return [
    //             'rekap' => $formattedData,
    //         ];
    //     } catch (\Throwable $th) {
    //         Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
    //         return 'Error: ' . $th->getMessage();
    //     }
    // }

    // Export untuk divisi tiktok
    public function exportTiktok(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = ItMultimediaTiktok::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapTiktok = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapTiktok->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapTiktok->map(function ($item) {
                $imagePath = public_path('images/it/multimediatiktok/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/multimediatiktok/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan

                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (exportTiktok): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi instagram
    public function exportInstagram(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = ItMultimediaInstagram::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapInstagram = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapInstagram->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapInstagram->map(function ($item) {
                $imagePath = public_path('images/it/multimediainstagram/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/multimediainstagram/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportInstagram): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi instagram
    public function exportBizdev(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanBizdevGambar::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapBizdev = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapBizdev->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            // Format data dengan path gambar
            $formattedData = $rekapBizdev->map(function ($item) {
                $imagePath = public_path('images/it/laporanbizdevgambar/' . $item->gambar);
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/laporanbizdevgambar/' . $item->gambar)
                        : asset('images/no-image.png'),
                    'Keterangan' => $item->keterangan,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportBizdev): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportKHPS(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = KasHutangPiutang::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $rekapKHPS = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapKHPS->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapKHPS->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Kas' => 'Rp ' .  number_format($item->kas, 0, ',', '.'),
                    'Hutang' => 'Rp ' .  number_format($item->hutang, 0, ',', '.'),
                    'Piutang' => 'Rp ' .  number_format($item->piutang, 0, ',', '.'),
                    'Stok' => 'Rp ' .  number_format($item->stok, 0, ',', '.'),
                ];
            });
            $totalKas = $rekapKHPS->sum('kas');
            $totalHutang = $rekapKHPS->sum('hutang');
            $totalPiutang = $rekapKHPS->sum('piutang');
            $totalStok = $rekapKHPS->sum('stok');

            $formattedKas = number_format($totalKas, 0, ',', '.');
            $formattedHutang = number_format($totalHutang, 0, ',', '.');
            $formattedPiutang = number_format($totalPiutang, 0, ',', '.');
            $formattedStok = number_format($totalStok, 0, ',', '.');

            $chartData = [
                'labels' => [
                    "Kas : Rp $formattedKas",
                    "Hutang : Rp $formattedHutang",
                    "Piutang : Rp $formattedPiutang",
                    "Stok : Rp $formattedStok",
                ],
                'datasets' => [
                    [
                        'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'], // Warna untuk pie chart
                        'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportKHPS): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportArusKas(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = ArusKas::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);
            $rekapArusKas = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($rekapArusKas->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $rekapArusKas->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Masuk' => 'Rp ' .  number_format($item->kas_masuk, 0, ',', '.'),
                    'Keluar' => 'Rp ' .  number_format($item->kas_keluar, 0, ',', '.'),
                ];
            });
            $kasMasuk = $rekapArusKas->sum('kas_masuk');
            $kasKeluar = $rekapArusKas->sum('kas_keluar');

            $formattedKasMasuk = number_format($kasMasuk, 0, ',', '.');
            $formattedKasKeluar = number_format($kasKeluar, 0, ',', '.');

            $chartData = [
                'labels' => [
                    "Kas : Rp $formattedKasMasuk",
                    "Keluar : Rp $formattedKasKeluar",

                ],
                'datasets' => [
                    [
                        'data' => [$kasMasuk, $kasKeluar],
                        'backgroundColor' => ['#1c64f2', '#ff2323'],
                        'hoverBackgroundColor' => ['#2b6cb0', '#dc2626'],
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (func exportArusKas): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    //Laporan SPI
    // Export untuk divisi HRGA
    public function exportLaporanSPI(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanSPI::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $laporanSPI = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($laporanSPI->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData =  $laporanSPI->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Aspek' => $item->aspek,
                    'Masalah' => $item->masalah,
                    'Solusi' => $item->solusi,
                    'Implementasi' => $item->implementasi,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting  (func exportLaporanSPI): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi HRGA
    public function exportLaporanSPIIT(Request $request)
    {
        try {
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m

            $instance = new self($startMonth, $endMonth);

            // Bangun query berdasarkan data constructor
            $query = LaporanSPITI::query();

            if ($request->has('filter')) {
                $instance->useFilter = filter_var($request->input('filter'), FILTER_VALIDATE_BOOLEAN);
            }
            $query = $instance->applyDateFilter($query);

            $laporanSPIIT = $query
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($laporanSPIIT->isEmpty()) {
                return 'Data tidak ditemukan.';
            }

            $formattedData = $laporanSPIIT->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Aspek' => $item->aspek,
                    'Masalah' => $item->masalah,
                    'Solusi' => $item->solusi,
                    'Implementasi' => $item->implementasi,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];
        } catch (\Throwable $th) {
            Log::error('Error exporting (exportLaporanSPIIT): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportTaxPlanning(Request $request)
{
     try {
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    $query = TaxPlanning::query();

    // Filter berdasarkan tanggal jika ada search
    if ($search) {
        $query->where('tanggal', 'LIKE', "%$search%");
    }

    // Filter berdasarkan range bulan-tahun jika keduanya diisi
    if ($startMonth && $endMonth) {
        try {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
        }
    }

    $allData = $query->get();

    $groupedByCompany = $allData->groupBy('nama_perusahaan');

    $companyNames = $groupedByCompany->keys()->toArray();

    $taxPlanningData = [];
    $totalPenjualanData = [];

    foreach ($companyNames as $companyName) {
        $companyItems = $groupedByCompany[$companyName];
        $taxPlanningData[] = $companyItems->sum('tax_planning');
        $totalPenjualanData[] = $companyItems->sum('total_penjualan');
    }

    $chartData = [
        'labels' => $companyNames,
        'datasets' => [
            [
                'label' => 'Total Tax Planning',
                'data' => $taxPlanningData,
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
            ],
            [
                'label' => 'Total Sales',
                'data' => $totalPenjualanData,
                'backgroundColor' => 'rgba(255, 159, 64, 0.7)',
            ],
        ],
    ];
        return [
                    'chart' => $chartData
                ];
                } catch (\Throwable $th) {
            Log::error('Error exporting (func exportTaxPlanning): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
}

