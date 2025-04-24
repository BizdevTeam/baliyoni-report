<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\KasHutangPiutang;
use App\Models\LaporanCuti;
use App\Models\LaporanDetrans;
use App\Models\LaporanHolding;
use App\Models\LaporanIzin;
use App\Models\LaporanNegosiasi;
use App\Models\LaporanOutlet;
use App\Models\LaporanPaketAdministrasi;
use App\Models\LaporanPerInstansi;
use App\Models\LaporanSakit;
use App\Models\LaporanSPI;
use App\Models\LaporanStok;
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

    public function __construct($data = 'March 2025') {
        $this->month = Carbon::createFromFormat('F Y', $data)->month;
        $this->year = Carbon::createFromFormat('F Y', $data)->year;
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
            $dataExportLaporanPenjualan = $this->safeExport(fn() => $this->exportRekapPenjualan($this->month, $this->year));

            $dataExportLaporanPenjualanPerusahaan = $this->safeExport(fn() => $this->exportRekapPenjualanPerusahaan($this->month, $this->year));

            $dataExportLaporanPaketAdministrasi = $this->safeExport(fn() => $this->exportLaporanPaketAdministrasi($this->month, $this->year));

            $dataExportStatusPaket = $this->safeExport(fn() => $this->exportStatusPaket($this->month, $this->year));

            $dataExportLaporanPerInstansi = $this->safeExport(fn() => $this->exportLaporanPerInstansi($this->month, $this->year));
    
            // === Untuk divisi Procurement ===
            $dataExportLaporanHolding = $this->safeExport(fn() => $this->exportLaporanHolding($this->month, $this->year)); 

            $dataExportLaporanStok = $this->safeExport(fn() => $this->exportLaporanStok($this->month, $this->year)); 

            $dataExportLaporanPembelianOutlet = 
            $this->safeExport(fn() => $this->exportLaporanPembelianOutlet($this->month, $this->year)); 

            $dataExportLaporanNegosiasi = $this->safeExport(fn() => $this->exportLaporanNegosiasi($this->month, $this->year));

            // === Untuk divisi Supports ===
            $dataExportRekapPendapatanASP = $this->safeExport(fn() => $this->exportRekapPendapatanASP($this->month, $this->year));

            $dataExportRekapPiutangASP = $this->safeExport(fn() => $this->exportRekapPiutangASP($this->month, $this->year));

            $dataLaporanPengiriman = $this->safeExport(fn() => $this->exportLaporanPengiriman($this->month, $this->year));

            // === Untuk divisi HRGA ===
            $dataLaporanSakit = $this->safeExport(fn() => $this->exportSakit($this->month, $this->year));

            $dataLaporanCuti = $this->safeExport(fn() => $this->exportCuti($this->month, $this->year));

            $dataLaporanIzin = $this->safeExport(fn() => $this->exportIzin($this->month, $this->year));

            $dataLaporanTerlambat = $this->safeExport(fn() => $this->exportTerlambat($this->month, $this->year));

            // === Untuk divisi Accounting ===
            $dataKHPS = $this->safeExport(fn() => $this->exportKHPS($this->month, $this->year));
            $dataArusKas = $this->safeExport(fn() => $this->exportArusKas($this->month, $this->year));

            // === Untuk divisi SPI ===
            $dataLaporanSPI = $this->safeExport(fn() => $this->exportLaporanSPI($this->month, $this->year));
    
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
                
            ))
            ->with('month', $this->month)
            ->with('year', $this->year);
    
        } catch (\Throwable $th) {
            Log::error('Error exporting all laporan (exp new): ' . $th->getMessage());
            return back()->withErrors($th->getMessage());
        }
    }    

    public function exportRekapPenjualan($month, $year) {
        try {
            $rekapPenjualan = RekapPenjualan::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapPenjualan->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportRekapPenjualanPerusahaan($month, $year) {
        try {
            $rekapPenjualanPerusahaan = RekapPenjualanPerusahaan::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapPenjualanPerusahaan->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
     
    public function exportLaporanPaketAdministrasi($month, $year) {
        try {
            $rekapLaporanPaketAdministrasi = LaporanPaketAdministrasi::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanPaketAdministrasi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportStatusPaket($month, $year) {
        try {
            $rekapStatusPaket = StatusPaket::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapStatusPaket->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportLaporanPerInstansi($month, $year) {
        try {
            $rekapLaporanPerInstansi = LaporanPerInstansi::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanPerInstansi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportLaporanHolding($month, $year) {
        try {
            $rekapLaporanHolding = LaporanHolding::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanHolding->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportLaporanStok($month, $year) {
        try {
            $rekapLaporanStok = LaporanStok::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanStok->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportLaporanPembelianOutlet($month, $year) {
        try {
            $rekapLaporanPembelianOutlet = LaporanOutlet::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanPembelianOutlet->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportLaporanNegosiasi($month, $year) {
        try {
            $rekapLaporanNegosiasi = LaporanNegosiasi::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapLaporanNegosiasi->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportRekapPendapatanASP($month, $year) {
        try {
            $rekapPendapatanASP = RekapPendapatanServisASP::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapPendapatanASP->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportRekapPiutangASP($month, $year) {
        try {
            $rekapPiutangServisASP = RekapPiutangServisASP::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            if ($rekapPiutangServisASP->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportLaporanPengiriman($month, $year)
{
    try {
        $rekapPengiriman = LaporanDetrans::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapPengiriman->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportSakit($month, $year) {
    try {
        $rekapSakit = LaporanSakit::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapSakit->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportCuti($month, $year) {
    try {
        $rekapCuti = LaporanCuti::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapCuti->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportIzin($month, $year) {
    try {
        $rekapIzin = LaporanIzin::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapIzin->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportTerlambat($month, $year) {
    try {
        $rekapTerlambat = LaporanTerlambat::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapTerlambat->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    //Export Accounting
    public function exportKHPS($month, $year) {
    try {
        $rekapKHPS = KasHutangPiutang::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapKHPS->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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

    public function exportArusKas($month, $year) {
    try {
        $rekapArusKas = ArusKas::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        if ($rekapArusKas->isEmpty()) {
            return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
    public function exportLaporanSPI($month, $year) {
        try {
            $laporanSPI = LaporanSPI::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();
    
            if ($laporanSPI->isEmpty()) {
                return 'Data tidak ditemukan untuk bulan ' . $month . ' tahun ' . $year;
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
}
