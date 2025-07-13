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
use App\Models\RekapPendapatanServisAsp;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\RekapPiutangServisAsp;
use App\Models\StatusPaket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ExportLaporanAll extends Controller
{
    private $month;
    private $year;
    private $startDate;
    private $endDate;
    private $useFilter = false;

    
    public function __construct()
    {
        // Set default date to current month and year if no filters are applied later
        $date = Carbon::now();
        $this->month = $date->month;
        $this->year = $date->year;
    }
    
    private function applyDateFilter($query, $tanggalColumn = 'tanggal')
    {
        // Only apply filter if the flag is explicitly set to true
        if (!$this->useFilter) {
            return $query;
        }
    
        if (isset($this->startDate) && isset($this->endDate)) {
            // Filter a date range. Using DB::raw to handle potential VARCHAR date columns.
            // NOTE: For better performance, it's highly recommended to change the column type to DATE or DATETIME in your database.
            $query->whereBetween(
                DB::raw("STR_TO_DATE($tanggalColumn, '%Y-%m-%d')"),
                [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')]
            );
        } elseif (isset($this->month) && isset($this->year)) {
            // Filter for a single month.
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

    private function safeExport(callable $callback)
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
            Log::error('Error during safeExport execution: ' . $e->getMessage());
            return ['rekap' => [], 'chart' => $emptyChart];
        }
    }

    public function exportAll(Request $request)
    {
        // Read range from the request input
        $start = $request->input('start_month');
        $end   = $request->input('end_month');

        // --- FIX: Initialize date properties on the CURRENT instance ($this) ---
        if ($start && $end) {
            // If both start and end month are provided, set up the date range filter.
            $this->startDate = Carbon::createFromFormat('Y-m', $start)->startOfMonth();
            $this->endDate = Carbon::createFromFormat('Y-m', $end)->endOfMonth();
            $this->useFilter = true; // IMPORTANT: Set the flag to true
            
            // Also update month/year for display purposes, e.g., using the start date.
            $this->month = $this->startDate->month;
            $this->year = $this->startDate->year;

        } else {
            // If no range is provided, the default values from __construct() will be used.
            $this->useFilter = false;
        }
        // --- END OF FIX ---

        $selected = $request->input('reports', []);
        $empty    = ['rekap'=>[], 'chart'=>['labels'=>[], 'datasets'=>[]]];

        // prepare variables for each possible report
        $data = array_fill_keys($selected, $empty);

        // Marketing
        if (in_array('penjualan',               $selected)) $data['penjualan']             = $this->safeExport(fn() => $this->exportRekapPenjualan($request));
        if (in_array('penjualan_perusahaan',    $selected)) $data['penjualan_perusahaan']  = $this->safeExport(fn() => $this->exportRekapPenjualanPerusahaan($request));
        if (in_array('paket_admin',             $selected)) $data['paket_admin']           = $this->safeExport(fn() => $this->exportLaporanPaketAdministrasi($request));
        if (in_array('status_paket',            $selected)) $data['status_paket']          = $this->safeExport(fn() => $this->exportStatusPaket($request));
        if (in_array('per_instansi',            $selected)) $data['per_instansi']          = $this->safeExport(fn() => $this->exportLaporanPerInstansi($request));

        // Procurement
        if (in_array('holding',                 $selected)) $data['holding']                = $this->safeExport(fn() => $this->exportLaporanHolding($request));
        if (in_array('stok',                    $selected)) $data['stok']                   = $this->safeExport(fn() => $this->exportLaporanStok($request));
        if (in_array('pembelian_outlet',        $selected)) $data['pembelian_outlet']       = $this->safeExport(fn() => $this->exportLaporanPembelianOutlet($request));
        if (in_array('negosiasi',               $selected)) $data['negosiasi']              = $this->safeExport(fn() => $this->exportLaporanNegosiasi($request));

        // Supports
        if (in_array('pendapatan_asp',          $selected)) $data['pendapatan_asp']          = $this->safeExport(fn() => $this->exportRekapPendapatanASP($request));
        if (in_array('piutang_asp',             $selected)) $data['piutang_asp']             = $this->safeExport(fn() => $this->exportRekapPiutangASP($request));
        if (in_array('pengiriman',              $selected)) $data['pengiriman']              = $this->safeExport(fn() => $this->exportLaporanPengiriman($request));

        // HRGA
        if (in_array('ptbos',                   $selected)) $data['ptbos']                   = $this->safeExport(fn() => $this->exportPTBOS($request));
        if (in_array('ijasa',                   $selected)) $data['ijasa']                   = $this->safeExport(fn() => $this->exportIJASA($request));
        if (in_array('ijasagambar',             $selected)) $data['ijasagambar']             = $this->safeExport(fn() => $this->exportIJASAGambar($request));
        if (in_array('sakit',                   $selected)) $data['sakit']                   = $this->safeExport(fn() => $this->exportSakit($request));
        if (in_array('cuti',                    $selected)) $data['cuti']                    = $this->safeExport(fn() => $this->exportCuti($request));
        if (in_array('izin',                    $selected)) $data['izin']                    = $this->safeExport(fn() => $this->exportIzin($request));
        if (in_array('terlambat',               $selected)) $data['terlambat']               = $this->safeExport(fn() => $this->exportTerlambat($request));

        // Accounting
        if (in_array('khps',                    $selected)) $data['khps']                    = $this->safeExport(fn() => $this->exportKHPS($request));
        if (in_array('laba_rugi',               $selected)) $data['laba_rugi']               = $this->safeExport(fn() => $this->exportLabaRugi($request));
        if (in_array('neraca',                  $selected)) $data['neraca']                  = $this->safeExport(fn() => $this->exportNeraca($request));
        if (in_array('rasio',                   $selected)) $data['rasio']                   = $this->safeExport(fn() => $this->exportRasio($request));
        if (in_array('ppn',                     $selected)) $data['ppn']                     = $this->safeExport(fn() => $this->exportPPn($request));
        if (in_array('arus_kas',                $selected)) $data['arus_kas']                = $this->safeExport(fn() => $this->exportArusKas($request));
        if (in_array('taxplanning',             $selected)) $data['taxplanning']             = $this->safeExport(fn() => $this->exportTaxPlanning($request));

        // SPI
        if (in_array('spi',                     $selected)) $data['spi']                     = $this->safeExport(fn() => $this->exportLaporanSPI($request));
        if (in_array('spiit',                   $selected)) $data['spiit']                   = $this->safeExport(fn() => $this->exportLaporanSPIIT($request));

        // IT
        if (in_array('tiktok',                  $selected)) $data['tiktok']                  = $this->safeExport(fn() => $this->exportTiktok($request));
        if (in_array('instagram',               $selected)) $data['instagram']               = $this->safeExport(fn() => $this->exportInstagram($request));
        if (in_array('bizdev',                  $selected)) $data['bizdev']                  = $this->safeExport(fn() => $this->exportBizdev($request));
        if (in_array('bizdev1',                 $selected)) $data['bizdev1']                 = $this->safeExport(fn() => $this->exportBizdev1($request));

        // render the view with only those data keys
        return view('exports.export', [
            'data'            => $data,
            'selectedReports' => $selected,
            'month'           => $this->month,
            'year'            => $this->year,
            'startDate'       => $this->startDate ? $this->startDate->format('Y-m') : null,
            'endDate'         => $this->endDate ? $this->endDate->format('Y-m') : null,
            'isFiltered'      => $this->useFilter
        ]);
    }
    
    public function exportRekapPenjualan(Request $request) 
    {
        try {
            $query = RekapPenjualan::query();
            $this->applyDateFilter($query, 'tanggal');
    
            $rekapPenjualan = $query->orderBy('tanggal', 'asc')->get();
    
            if ($rekapPenjualan->isEmpty()) {
                return ['rekap' => [], 'chart' => []];
            }
    
            $formattedData = $rekapPenjualan->map(function ($item) {
                return [
                    'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
                ];
            });
    
            $labels = $rekapPenjualan->map(function ($item) {
                return Carbon::parse($item->tanggal)->translatedFormat('F Y');
            })->toArray();
    
            $data = $rekapPenjualan->pluck('total_penjualan')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);
    
            $chartData = [
                'labels' => $labels,
                'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]],
            ];
    
            return ['rekap' => $formattedData, 'chart' => $chartData];
    
        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportRekapPenjualan): ' . $th->getMessage());
            return ['rekap' => [], 'chart' => []];
        }
    }
    
    public function exportRekapPenjualanPerusahaan(Request $request) {
        try {
        $query = RekapPenjualanPerusahaan::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPenjualanPerusahaan = $query
        ->orderBy('tanggal','asc')
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

            $labels = $rekapPenjualanPerusahaan->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->perusahaan->nama_perusahaan.' - ' . $formattedDate;
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
            Log::error('Error exporting  (func ExLapAll exportRekapPenjualanPerusahaan): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
     
    public function exportLaporanPaketAdministrasi(Request $request) {
        try {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanPaketAdministrasi = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapLaporanPaketAdministrasi->isEmpty()) {
            return 'Data tidak ditemukan.';
        }
        $formattedData = $rekapLaporanPaketAdministrasi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Website' => $item->website,
                    'Total Paket' =>number_format($item->total_paket, 0, ',', '.'),
                ];
            });
        
            $labels = $rekapLaporanPaketAdministrasi->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->website.' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanPaketAdministrasi->pluck('total_paket')->toArray();
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
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportLaporanPaketAdministrasi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportStatusPaket(Request $request) {
        try {
    
        $query = StatusPaket::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapStatusPaket = $query
        ->orderBy('tanggal','asc')
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
        $labels = $rekapStatusPaket->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->status.' - ' . $formattedDate;
        })->toArray();

        $data = $rekapStatusPaket->pluck('total_paket')->toArray();
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
            'rekap' => $formattedData,
            'chart' => $chartData,
        ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportStatusPaket): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportLaporanPerInstansi(Request $request) {
        try {
        $query = LaporanPerInstansi::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanPerInstansi = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapLaporanPerInstansi->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->instansi.' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanPerInstansi->pluck('nilai')->toArray();
            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Nilai',
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
            Log::error('Error exporting (func ExLapAll exportLaporanPerInstansi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Procurement
    public function exportLaporanHolding(Request $request) {
        try {

        $query = LaporanHolding::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanHolding = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapLaporanHolding->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->perusahaan->nama_perusahaan.' - ' . $formattedDate;
            })->toArray();

            $data = $rekapLaporanHolding->pluck('nilai')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Nilai',
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
            Log::error('Error exporting (func ExLapAll exportLaporanHolding): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanStok(Request $request) {
        try {
    
        $query = LaporanStok::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanStok = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapLaporanStok->map(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            })->toArray();

            $data = $rekapLaporanStok->pluck('stok')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Stok',
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
            Log::error('Error exporting (func ExLapAll exportLaporanStok): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPembelianOutlet(Request $request) {
        try {

        $query = LaporanOutlet::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanPembelianOutlet = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapLaporanPembelianOutlet->map(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            })->toArray();

            $data = $rekapLaporanPembelianOutlet->pluck('total_pembelian')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Pembelian',
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
            Log::error('Error exporting (func ExLapAll exportLaporanPembelianOutlet): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanNegosiasi(Request $request) {
        try {

        $query = LaporanNegosiasi::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLaporanNegosiasi = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapLaporanNegosiasi->map(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            })->toArray();

            $data = $rekapLaporanNegosiasi->pluck('total_negosiasi')->toArray();

            $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Negosiasi',
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
            Log::error('Error exporting (func ExLapAll exportLaporanNegosiasi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Supports
    public function exportRekapPendapatanASP(Request $request) {
        try {

        $query = RekapPendapatanServisAsp::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPendapatanASP = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapPendapatanASP->map(function ($item) {
                return $item->pelaksana . ' ('. 'Rp'. ' ' . number_format($item->nilai_pendapatan) . ')';
            })->toArray();    
            $data = $rekapPendapatanASP->pluck('nilai_pendapatan')->toArray();
        

            $backgroundColors = $rekapPendapatanASP->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Nilai Pendapatan',
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
            Log::error('Error exporting (func ExLapAll exportRekapPendapatanASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportRekapPiutangASP(Request $request) {
        try {

        $query = RekapPiutangServisAsp::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPiutangServisASP = $query
        ->orderBy('tanggal','asc')
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
            $labels = $rekapPiutangServisASP->map(function ($item) {
                return $item->pelaksana . ' ('. 'Rp'. ' ' . number_format($item->nilai_piutang) . ')';
            })->toArray();    
            $data = $rekapPiutangServisASP->pluck('nilai_piutang')->toArray();
        

            $backgroundColors = $rekapPiutangServisASP->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Nilai Piutang',
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
            Log::error('Error exporting (func ExLapAll exportRekapPiutangASP): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPengiriman(Request $request)
{
    try {

        $query = LaporanDetrans::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPengiriman = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapPengiriman->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

        $formattedData = $rekapPengiriman->map(function ($item) {
            return [
                'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Pelaksana' => $item->pelaksana,
                'Total' => 'Rp ' . number_format($item->total_pengiriman, 0, ',', '.'),
            ];
        });

        $months = $rekapPengiriman
            ->sortBy('tanggal')
            ->map(fn($item) => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y'))
            ->unique()
            ->values()
            ->toArray();

        $groupedData = [];
        foreach ($rekapPengiriman as $item) {
            $bulan = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            $groupedData[$item->pelaksana][$bulan][] = $item->total_pengiriman;
        }

        $colorMap = [
            'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
            'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
        ];
        $defaultColor = 'rgba(128, 128, 128, 0.7)';

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
        Log::error('Error exporting (exportLaporanPengiriman): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    // Export untuk divisi HRGA
    public function exportPTBOS(Request $request) {
    try {

        $query = LaporanPtBos::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPTBOS = $query
        ->orderBy('tanggal','asc')
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
        Log::error('Error exporting  (func ExLapAll exportPTBOS): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    public function exportIJASA(Request $request) {
    try {

        $query = LaporanIjasa::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapIJASA = $query
        ->orderBy('tanggal','asc')
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
        Log::error('Error exporting  (func ExLapAll exportIJASA): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

public function exportIJASAGambar(Request $request)
{
    try {

        $query = IjasaGambar::query();
        $this->applyDateFilter($query, 'tanggal');

        $ijasaGambar = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($ijasaGambar->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

        $formattedData = $ijasaGambar->map(function ($item) {
        $imagePath = public_path('images/hrga/ijasagambar/' . $item->gambar);
            return [
                'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                    ? asset('images/hrga/ijasagambar/' . $item->gambar)
                    : asset('images/no-image.png'),
            ];
        });

        return [
            'rekap' => $formattedData,
        ];

    } catch (\Throwable $th) {
        Log::error('Error exporting (func ExLapAll exportIJASAGambar): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    public function exportSakit(Request $request) {
    try {

        $query = LaporanSakit::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapSakit = $query
        ->orderBy('tanggal','asc')
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

        $labels = $rekapSakit->map(function ($item) {
            return $item->nama;
        })->toArray();

        $data = $rekapSakit->pluck('total_sakit')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Sakit',
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
        Log::error('Error exporting  (func ExLapAll exportSakit): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    public function exportCuti(Request $request) {
    try {
 
        $query = LaporanCuti::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapCuti = $query
        ->orderBy('tanggal','asc')
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

        $labels = $rekapCuti->map(function ($item) {
            return $item->nama;
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
        Log::error('Error exporting  (func ExLapAll exportCuti): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    public function exportIzin(Request $request) {
    try {

        $query = LaporanIzin::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapIzin = $query
        ->orderBy('tanggal','asc')
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

        $labels = $rekapIzin->map(function ($item) {
            return $item->nama;
        })->toArray();

        $data = $rekapIzin->pluck('total_izin')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Izin',
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
        Log::error('Error exporting  (func ExLapAll exportIzin): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    public function exportTerlambat(Request $request) {
    try {

        $query = LaporanTerlambat::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapTerlambat = $query
        ->orderBy('tanggal','asc')
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

        $labels = $rekapTerlambat->map(function ($item) {
            return $item->nama;
        })->toArray();

        $data = $rekapTerlambat->pluck('total_terlambat')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

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

        return [
            'rekap' => $formattedData,
            'chart' => $chartData,
        ];

    } catch (\Throwable $th) {
        Log::error('Error exporting  (func ExLapAll exportTerlambat): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    public function exportLabaRugi(Request $request)
    {
        try {

        $query = LaporanLabaRugi::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapLabaRugi = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapLabaRugi->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapLabaRugi->map(function ($item) {
            $imagePath = public_path('images/accounting/labarugi/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/labarugi/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportLabaRugi): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportNeraca(Request $request)
    {
        try {

        $query = LaporanNeraca::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapNeraca = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapNeraca->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapNeraca->map(function ($item) {
            $imagePath = public_path('images/accounting/neraca/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/neraca/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportNeraca): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportRasio(Request $request)
    {
        try {

        $query = LaporanRasio::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapRasio = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapRasio->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapRasio->map(function ($item) {
            $imagePath = public_path('images/accounting/rasio/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/rasio/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportRasio): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportPPn(Request $request)
    {
        try {

        $query = LaporanPpn::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapPPn = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapPPn->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapPPn->map(function ($item) {
            $imagePath = public_path('images/accounting/ppn/' . $item->thumbnail);
                return [
                    'Gambar' => (!empty($item->thumbnail) && file_exists($imagePath))
                        ? asset('images/accounting/ppn/' . $item->thumbnail)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportPPn): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportTaxPlanning(Request $request)
    {
        try {

        $query = LaporanTaxPlaning::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapTaxPlanning = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapTaxPlanning->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapTaxPlanning->map(function ($item) {
            $imagePath = public_path('images/accounting/taxplaning/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/accounting/taxplaning/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportTaxPlanning): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportTiktok(Request $request)
    {
        try {
    
        $query = ItMultimediaTiktok::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapTiktok = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapTiktok->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapTiktok->map(function ($item) {
            $imagePath = public_path('images/it/multimediatiktok/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/multimediatiktok/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportTiktok): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportInstagram(Request $request)
    {
        try {

        $query = ItMultimediaInstagram::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapInstagram = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapInstagram->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapInstagram->map(function ($item) {
            $imagePath = public_path('images/it/multimediainstagram/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/multimediainstagram/' . $item->gambar)
                        : asset('images/no-image.png'),
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportInstagram): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportBizdev(Request $request)
    {
        try {

        $query = LaporanBizdevGambar::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapBizdev = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapBizdev->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapBizdev->map(function ($item) {
            $imagePath = public_path('images/it/laporanbizdevgambar/' . $item->gambar);
                return [
                    'Gambar' => (!empty($item->gambar) && file_exists($imagePath))
                        ? asset('images/it/laporanbizdevgambar/' . $item->gambar)
                        : asset('images/no-image.png'),

                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportBizdev Bag. Kendala): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportBizdev1(Request $request)
    {
        try {

        $query = LaporanBizdevGambar::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapBizdev = $query
        ->orderBy('tanggal','asc')
        ->get();

        if ($rekapBizdev->isEmpty()) {
            return 'Data tidak ditemukan.';
        }

            $formattedData = $rekapBizdev->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Kendala' => $item->kendala,
                ];
            });

            return [
                'rekap' => $formattedData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportBizdev Bag. Kendala): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportKHPS(Request $request) {
    try {

        $query = KasHutangPiutang::query();
        $this->applyDateFilter($query, 'tanggal');

        $rekapKHPS = $query
        ->orderBy('tanggal','asc')
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
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'],
                        'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                    ],
                ],
            ];

            return [
                'rekap' => $formattedData,
                'chart' => $chartData,
            ];

        } catch (\Throwable $th) {
            Log::error('Error exporting (func ExLapAll exportKHPS): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

        public function exportArusKas(Request $request) {
        try {

            $query = ArusKas::query();
            $this->applyDateFilter($query, 'tanggal');

            $rekapArusKas = $query
            ->orderBy('tanggal','asc')
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
                    "Kas Masuk : Rp $formattedKasMasuk",
                    "Kas Keluar : Rp $formattedKasKeluar",

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
            Log::error('Error exporting (func ExLapAll exportArusKas): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
        public function exportLaporanSPI(Request $request) {
            try {

            $query = LaporanSPI::query();
            $this->applyDateFilter($query, 'tanggal');

            $laporanSPI = $query
            ->orderBy('tanggal','asc')
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
                Log::error('Error exporting  (func ExLapAll exportLaporanSPI): ' . $th->getMessage());
                return 'Error: ' . $th->getMessage();
            }
        }
        public function exportLaporanSPIIT(Request $request)
    {
        try {

            $query = LaporanSPITI::query();
            $this->applyDateFilter($query, 'tanggal');

            $laporanSPIIT = $query
            ->orderBy('tanggal','asc')
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
            Log::error('Error exporting (func ExLapAll exportLaporanSPIIT): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }    
}