<?php

namespace App\Http\Controllers;

use App\Models\LaporanPaketAdministrasi;
use App\Models\LaporanPerInstansi;
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
            $dataExportLaporanPenjualan = $this->exportRekapPenjualan($this->month, $this->year);
            $dataExportLaporanPenjualanPerusahaan = $this->exportRekapPenjualanPerusahaan($this->month, $this->year);
            $dataExportLaporanPaketAdministrasi = $this->exportLaporanPaketAdministrasi($this->month, $this->year);
            $dataExportStatusPaket = $this->exportStatusPaket($this->month, $this->year);
            $dataExportLaporanPerInstansi = $this->exportLaporanPerInstansi($this->month, $this->year);

            // dd($dataExportLaporanPenjualan, $dataExportLaporanPenjualanPerusahaan, $dataExportLaporanPaketAdministrasi, $dataExportStatusPaket, $dataExportLaporanPerInstansi);

            return view('exports.all-laporan', compact('dataExportLaporanPenjualan', 'dataExportLaporanPenjualanPerusahaan', 'dataExportLaporanPaketAdministrasi', 'dataExportStatusPaket', 'dataExportLaporanPerInstansi'));

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
                return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
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

            return $rekapLaporanPaketAdministrasi->map(function ($item) {
                return [
                    'Tanggal' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                    'Website' => $item->website,
                    'Total Paket' => 'Rp ' . number_format($item->total_paket, 0, ',', '.'),
                ];
            });

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

            return $rekapStatusPaket->map(function ($item) {
                return [
                    'Tanggal' => $item->tanggal,
                    'Status' => $item->status,
                    'Total Penjualan' => $item->total_paket,
                ];
            });

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

            return $rekapLaporanPerInstansi->map(function ($item) {
                return [
                    'Tanggal' => $item->tanggal,
                    'Instansi' => $item->instansi,
                    'Nilai' => $item->nilai,
                ];
            });
            

        } catch (\Throwable $th) {
            Log::error('Error exporting (exp new): ' . $th->getMessage());
            return 'Error: ' . $th->getMessage();
        }
    }
}
