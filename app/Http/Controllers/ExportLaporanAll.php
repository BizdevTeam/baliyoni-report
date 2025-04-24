<?php

namespace App\Http\Controllers;

use App\Models\LaporanHolding;
use App\Models\LaporanNegosiasi;
use App\Models\LaporanOutlet;
use App\Models\LaporanPaketAdministrasi;
use App\Models\LaporanPerInstansi;
use App\Models\LaporanStok;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
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

    public function exportAll() {
        try {
            // kirim month dan year ke exportRekapPenjualan

            // Untuk divisi Marketing
            $dataExportLaporanPenjualan = $this->exportRekapPenjualan($this->month, $this->year);

            $dataExportLaporanPenjualanPerusahaan = $this->exportRekapPenjualanPerusahaan($this->month, $this->year);

            $dataExportLaporanPaketAdministrasi = $this->exportLaporanPaketAdministrasi($this->month, $this->year);
            
            $dataExportStatusPaket = $this->exportStatusPaket($this->month, $this->year);

            $dataExportLaporanPerInstansi = $this->exportLaporanPerInstansi($this->month, $this->year);

            // Untuk divisi Procurement
            $dataExportLaporanHolding = $this->exportLaporanHolding($this->month, $this->year);

            $dataExportLaporanStok = $this->exportLaporanStok($this->month, $this->year);

            $dataExportLaporanPembelianOutlet = $this->exportLaporanPembelianOutlet($this->month, $this->year);

            $dataExportLaporanNegosiasi = $this->exportLaporanNegosiasi($this->month, $this->year);

            // dd($dataExportLaporanPenjualan, $dataExportLaporanPenjualanPerusahaan, $dataExportLaporanPaketAdministrasi, $dataExportStatusPaket, $dataExportLaporanPerInstansi);

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
}
