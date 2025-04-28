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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExportLaporanAll extends Controller
{
    private $month;
    private $year;

    // public function __construct($data = 'March 2025') {
    //     $this->month = Carbon::createFromFormat('F Y', $data)->month;
    //     $this->year = Carbon::createFromFormat('F Y', $data)->year;
    // }
       
    public function __construct($startMonth = null, $endMonth = null)
{
    if ($startMonth && $endMonth) {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $this->month = $startDate->month;
        $this->year = $startDate->year;
    } elseif ($startMonth) {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $startMonth);
        $this->month = $date->month;
        $this->year = $date->year;
    } elseif ($endMonth) {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $endMonth);
        $this->month = $date->month;
        $this->year = $date->year;
    } else {
        $date = \Carbon\Carbon::now();
        $this->month = $date->month;
        $this->year = $date->year;
    }
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
            return ['rekap' => [], 'chart' => $emptyChart];
        }
    }

    public function exportAll() {
        try {
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
            $dataExportLaporanPenjualan = $this->safeExport(fn() => $this->exportRekapPenjualan(request()));

            $dataExportLaporanPenjualanPerusahaan = $this->safeExport(fn() => $this->exportRekapPenjualanPerusahaan(request()));

            $dataExportLaporanPaketAdministrasi = $this->safeExport(fn() => $this->exportLaporanPaketAdministrasi(request()));

            $dataExportStatusPaket = $this->safeExport(fn() => $this->exportStatusPaket(request()));

            $dataExportLaporanPerInstansi = $this->safeExport(fn() => $this->exportLaporanPerInstansi(request()));
    
            // === Untuk divisi Procurement ===
            $dataExportLaporanHolding = $this->safeExport(fn() => $this->exportLaporanHolding(request())); 
            $dataExportLaporanStok = $this->safeExport(fn() => $this->exportLaporanStok(request())); 
            $dataExportLaporanPembelianOutlet = 
            $this->safeExport(fn() => $this->exportLaporanPembelianOutlet(request())); 
            $dataExportLaporanNegosiasi = $this->safeExport(fn() => $this->exportLaporanNegosiasi(request()));

            // === Untuk divisi Supports ===
            $dataExportRekapPendapatanASP = $this->safeExport(fn() => $this->exportRekapPendapatanASP(request()));
            $dataExportRekapPiutangASP = $this->safeExport(fn() => $this->exportRekapPiutangASP(request()));
            $dataLaporanPengiriman = $this->safeExport(fn() => $this->exportLaporanPengiriman(request()));

            // === Untuk divisi HRGA ===
            $dataPTBOS = $this->safeExport(fn() => $this->exportPTBOS(request()));
            $dataIJASA = $this->safeExport(fn() => $this->exportIJASA(request()));
            $dataIJASAGambar = $this->safeExport(fn() => $this->exportIJASAGambar(request()));
            $dataLaporanSakit = $this->safeExport(fn() => $this->exportSakit(request()));
            $dataLaporanCuti = $this->safeExport(fn() => $this->exportCuti(request()));
            $dataLaporanIzin = $this->safeExport(fn() => $this->exportIzin(request()));
            $dataLaporanTerlambat = $this->safeExport(fn() => $this->exportTerlambat(request()));

            // === Untuk divisi Accounting ===
            $dataKHPS = $this->safeExport(fn() => $this->exportKHPS(request()));
            $dataLabaRugi = $this->safeExport(fn() => $this->exportLabaRugi(request()));
            $dataNeraca = $this ->safeExport(fn() => $this->exportNeraca(request()));
            $dataRasio = $this->safeExport(fn() => $this->exportRasio(request()));
            $dataPPn = $this->safeExport(fn() => $this->exportPPn(request()));
            $dataArusKas = $this->safeExport(fn() => $this->exportArusKas(request()));
            $dataTaxPlanning = $this->safeExport(fn() => $this->exportTaxPlanning(request()));

            // === Untuk divisi SPI ===
            $dataLaporanSPI = $this->safeExport(fn() => $this->exportLaporanSPI(request()));
            $dataLaporanSPIIT = $this->safeExport(fn() => $this->exportLaporanSPIIT(request()));

            // IT
            $dataTiktok = $this->safeExport(fn() => $this->exportTiktok(request()));
            $dataInstagram = $this->safeExport(fn() => $this->exportInstagram(request()));
            $dataBizdev = $this->safeExport(fn() => $this->exportBizdev(request()));

    
            return view('exports.all-laporan', compact(
                'dataExportLaporanPenjualan',
                'dataExportLaporanPenjualanPerusahaan',
                'dataExportLaporanPaketAdministrasi',
                'dataExportStatusPaket',
                'dataExportLaporanPerInstansi',
                'dataExportLaporanHolding',
                'dataExportLaporanStok',
                'dataExportLaporanPembelianOutlet',
                'dataExportLaporanNegosiasi',
                'dataExportRekapPendapatanASP',
                'dataExportRekapPiutangASP',
                'dataLaporanPengiriman',
                'dataLaporanSakit',
                'dataLaporanCuti',
                'dataLaporanIzin',
                'dataLaporanTerlambat',
                'dataKHPS',
                'dataArusKas',
                'dataLaporanSPI',
                'dataLaporanSPIIT',
                'dataArusKas',
                'dataLabaRugi',
                'dataNeraca',
                'dataRasio',
                'dataPPn',
                'dataTaxPlanning',
                'dataTiktok',
                'dataInstagram',
                'dataBizdev',
                'dataPTBOS',
                'dataIJASA',
                'dataIJASAGambar',
                
            ))
            ->with('month', $this->month)
            ->with('year', $this->year);
    
        } catch (\Throwable $th) {
            Log::error('Error exporting all laporan (exp new): ' . $th->getMessage());
            return back()->withErrors($th->getMessage());
        }
    }    

    public function exportRekapPenjualan(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapPenjualan = RekapPenjualan::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapPenjualan->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            $formattedData =  $rekapPenjualan->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
                ];
            });

            // Siapkan data untuk chart
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
            Log::error('Error exporting  (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportRekapPenjualanPerusahaan(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapPenjualanPerusahaan = RekapPenjualanPerusahaan::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapPenjualanPerusahaan->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            Log::error('Error exporting  (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
     
    public function exportLaporanPaketAdministrasi(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanPaketAdministrasi = LaporanPaketAdministrasi::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanPaketAdministrasi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            $formattedData = $rekapLaporanPaketAdministrasi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Website' => $item->website,
                    'Total Paket' =>number_format($item->total_paket, 0, ',', '.'),
                ];
            });
        
            // Siapkan data untuk chart
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportStatusPaket(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapStatusPaket = StatusPaket::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapStatusPaket->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            return $item->status.' - ' . $formattedDate;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    public function exportLaporanPerInstansi(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanPerInstansi = LaporanPerInstansi::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanPerInstansi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
                return $item->instansi.' - ' . $formattedDate;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Procurement
    public function exportLaporanHolding(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanHolding = LaporanHolding::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanHolding->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
                return $item->perusahaan->nama_perusahaan.' - ' . $formattedDate;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanStok(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanStok = LaporanStok::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanStok->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPembelianOutlet(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanPembelianOutlet = LaporanOutlet::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanPembelianOutlet->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanNegosiasi(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLaporanNegosiasi = LaporanNegosiasi::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLaporanNegosiasi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi Supports
    public function exportRekapPendapatanASP(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapPendapatanASP = RekapPendapatanServisASP::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapPendapatanASP->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
                return $item->pelaksana . ' ('. 'Rp'. ' ' . number_format($item->nilai_pendapatan) . ')';
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportRekapPiutangASP(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapPiutangServisASP = RekapPiutangServisASP::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapPiutangServisASP->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
                return $item->pelaksana . ' ('. 'Rp'. ' ' . number_format($item->nilai_piutang) . ')';
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
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportLaporanPengiriman(Request $request)
{
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapPengiriman = LaporanDetrans::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapPengiriman->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting pengiriman: ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    // Export untuk divisi HRGA
    public function exportPTBOS(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapPTBOS = LaporanPtBos::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapPTBOS->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    // Export untuk divisi HRGA
    public function exportIJASA(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapIJASA = LaporanIjasa::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapIJASA->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

// Export untuk divisi tiktok
public function exportIJASAGambar(Request $request)
{
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $ijasaGambar = IjasaGambar::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($ijasaGambar->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
        }

        // Format data dengan path gambar
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
        Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    // Export untuk divisi HRGA
    public function exportSakit(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapSakit = LaporanSakit::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapSakit->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
        }

        $formattedData =  $rekapSakit->map(function ($item) {
            return [
                'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Nama' => $item->nama,
                'Total' => number_format($item->total_sakit, 0, ',', '.'),
            ];
        });

        // Siapkan data untuk chart
        $labels = $rekapSakit->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    // Export untuk divisi HRGA
    public function exportCuti(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapCuti = LaporanCuti::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapCuti->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
        }

        $formattedData =  $rekapCuti->map(function ($item) {
            return [
                'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Nama' => $item->nama,
                'Total' => number_format($item->total_cuti, 0, ',', '.'),
            ];
        });

        // Siapkan data untuk chart
        $labels = $rekapCuti->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    // Export untuk divisi HRGA
    public function exportIzin(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapIzin = LaporanIzin::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapIzin->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
        }

        $formattedData =  $rekapIzin->map(function ($item) {
            return [
                'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Nama' => $item->nama,
                'Total' => number_format($item->total_izin, 0, ',', '.'),
            ];
        });

        // Siapkan data untuk chart
        $labels = $rekapIzin->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    // Export untuk divisi HRGA
    public function exportTerlambat(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapTerlambat = LaporanTerlambat::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapTerlambat->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
        }

        $formattedData =  $rekapTerlambat->map(function ($item) {
            return [
                'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Nama' => $item->nama,
                'Total' => number_format($item->total_terlambat, 0, ',', '.'),
            ];
        });

        // Siapkan data untuk chart
        $labels = $rekapTerlambat->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
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
        Log::error('Error exporting  (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    // Export untuk divisi laba rugi
    public function exportLabaRugi(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapLabaRugi = LaporanLabaRugi::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapLabaRugi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportNeraca(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapNeraca = LaporanNeraca::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapNeraca->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportRasio(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapRasio = LaporanRasio::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapRasio->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi laba rugi
    public function exportPPn(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapPPn = LaporanPpn::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapPPn->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi taxplanning
    public function exportTaxPlanning(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapTaxPlanning = LaporanTaxPlaning::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapTaxPlanning->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    // Export untuk divisi tiktok
    public function exportTiktok(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapTiktok = ItMultimediaTiktok::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapTiktok->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi instagram
    public function exportInstagram(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapInstagram = ItMultimediaInstagram::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapInstagram->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi instagram
    public function exportBizdev(Request $request)
    {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $rekapBizdev = LaporanBizdevGambar::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();

            if ($rekapBizdev->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
            }

            // Format data dengan path gambar
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
            Log::error('Error exporting (exp HRGA): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }

    public function exportKHPS(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapKHPS = KasHutangPiutang::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapKHPS->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

    public function exportArusKas(Request $request) {
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $rekapArusKas = ArusKas::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($rekapArusKas->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting (exp new): ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}
    //Laporan SPI
    // Export untuk divisi HRGA
    public function exportLaporanSPI(Request $request) {
        try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
            $startMonth = $request->input('start_month'); // format Y-m
            $endMonth = $request->input('end_month');     // format Y-m
            $this->__construct($startMonth, $endMonth);

            $laporanSPI = LaporanSPI::whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->get();
    
            if ($laporanSPI->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
            Log::error('Error exporting  (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
    // Export untuk divisi HRGA
    public function exportLaporanSPIIT(Request $request)
{
    try {
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);
        $startMonth = $request->input('start_month'); // format Y-m
        $endMonth = $request->input('end_month');     // format Y-m

        // Panggil constructor
        $this->__construct($startMonth, $endMonth);

        $laporanSPIIT = LaporanSPITI::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        if ($laporanSPIIT->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $this->month . ' tahun ' . $this->year;
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
        Log::error('Error exporting: ' . $th->getMessage());
        return 'Error: ' . $th->getMessage();
    }
}

}
