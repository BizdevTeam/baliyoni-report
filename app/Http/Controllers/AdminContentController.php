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
    private $month;
    private $year;
    private $startDate;
    private $endDate;
    private $useFilter = false;

    /**
     * Constructor untuk menetapkan tanggal default (bulan dan tahun saat ini).
     */
    public function __construct()
    {
        $date = Carbon::now();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    /**
     * Menerapkan filter tanggal ke query yang diberikan.
     * Logika ini disalin dari ExportLaporanAll untuk konsistensi.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tanggalColumn
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDateFilter($query, $tanggalColumn = 'tanggal')
    {
        // Hanya terapkan filter jika flag useFilter diatur secara eksplisit ke true.
        if (!$this->useFilter) {
            return $query;
        }

        if (isset($this->startDate) && isset($this->endDate)) {
            // Filter rentang tanggal. Menggunakan DB::raw untuk menangani kolom tanggal VARCHAR.
            $query->whereBetween(
                DB::raw("STR_TO_DATE($tanggalColumn, '%Y-%m-%d')"),
                [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')]
            );
        } elseif (isset($this->month) && isset($this->year)) {
            // Filter untuk satu bulan.
            $search = sprintf('%04d-%02d', $this->year, $this->month);
            $query->whereRaw("DATE_FORMAT(STR_TO_DATE($tanggalColumn, '%Y-%m-%d'), '%Y-%m') = ?", [$search]);
        }

        return $query;
    }

    /**
     * Fungsi untuk menghasilkan warna RGBA acak.
     *
     * @return string
     */
    public function getRandomRGBA()
    {
        $opacity = 0.7;
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    /**
     * Wrapper aman untuk mengeksekusi fungsi dan mengembalikan data view.
     *
     * @param callable $callback
     * @return array
     */
    private function safeView(callable $callback)
    {
        $emptyChart = [
            'labels' => [],
            'datasets' => [['data' => [], 'backgroundColor' => []]]
        ];

        try {
            $result = $callback();
            // Pastikan struktur yang dikembalikan selalu array dengan kunci yang diharapkan
            if (!is_array($result)) {
                return ['rekap' => [], 'chart' => $emptyChart];
            }
            return [
                'rekap' => $result['rekap'] ?? [],
                'chart' => $result['chart'] ?? $emptyChart,
            ];
        } catch (\Throwable $e) {
            Log::error('Error during safeView execution: ' . $e->getMessage());
            return ['rekap' => [], 'chart' => $emptyChart];
        }
    }

    /**
     * Metode utama untuk mengambil dan memproses semua data untuk dashboard admin.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function adminContent(Request $request)
    {
        try {
            // --- LOGIKA FILTER TERPUSAT ---
            $start = $request->input('start_month');
            $end = $request->input('end_month');

            if ($start && $end) {
                // Jika rentang tanggal diberikan, atur properti filter.
                $this->startDate = Carbon::createFromFormat('Y-m', $start)->startOfMonth();
                $this->endDate = Carbon::createFromFormat('Y-m', $end)->endOfMonth();
                $this->useFilter = true; // Aktifkan flag filter
                
                // Perbarui bulan/tahun untuk tujuan tampilan.
                $this->month = $this->startDate->month;
                $this->year = $this->startDate->year;
            } else {
                // Jika tidak ada rentang, gunakan default dari constructor.
                $this->useFilter = false;
            }
            // --- AKHIR LOGIKA FILTER ---

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
                'dataExportLaporanPenjualan', 'dataExportLaporanPenjualanPerusahaan', 'dataTotalLaporanPenjualanPerusahaan',
                'dataExportLaporanPaketAdministrasi', 'dataTotalLaporanPaketAdministrasi', 'dataExportStatusPaket',
                'dataTotalStatusPaket', 'dataExportLaporanPerInstansi', 'dataTotalInstansi', 'dataExportLaporanHolding',
                'dataTotalLaporanHolding', 'dataExportLaporanStok', 'dataExportLaporanPembelianOutlet', 'dataExportLaporanNegosiasi',
                'dataExportRekapPendapatanASP', 'dataTotalRekapPendapatanASP', 'dataExportRekapPiutangASP', 'dataTotalRekapPiutangASP',
                'dataLaporanPengiriman', 'dataLaporanSakit', 'dataTotalSakit', 'dataLaporanCuti', 'dataTotalCuti',
                'dataLaporanIzin', 'dataTotalIzin', 'dataLaporanTerlambat', 'dataTotalTerlambat', 'dataKHPS',
                'dataArusKas', 'dataLaporanSPI', 'dataLaporanSPIIT', 'dataLabaRugi', 'dataNeraca', 'dataRasio',
                'dataPPn', 'dataTaxPlanningReport', 'dataTiktok', 'dataInstagram', 'dataBizdev', 'dataPTBOS',
                'dataIJASA', 'dataIJASAGambar'
            ))
            ->with('month', $this->month)
            ->with('year', $this->year)
            ->with('filtered', $this->useFilter);

        } catch (\Throwable $th) {
            Log::error('Error in adminContent: ' . $th->getMessage());
            return back()->withErrors('Terjadi kesalahan saat memuat data dashboard.');
        }
    }

    public function index(Request $request)
    {
        return $this->adminContent($request);
    }
    
    // ===================================================================
    // KUMPULAN FUNGSI UNTUK MENGAMBIL DATA (SEKARANG LEBIH BERSIH)
    // ===================================================================

    public function exportRekapPenjualan(Request $request)
    {
        $query = RekapPenjualan::query();
        $this->applyDateFilter($query);
        $rekapPenjualan = $query->orderBy('tanggal', 'asc')->get();

        if ($rekapPenjualan->isEmpty()) return [];

        $formattedData = $rekapPenjualan->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
        ]);

        $labels = $rekapPenjualan->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekapPenjualan->pluck('total_penjualan')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportRekapPenjualanPerusahaan(Request $request)
    {
        $query = RekapPenjualanPerusahaan::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Perusahaan' => $item->perusahaan->nama_perusahaan,
            'Total Penjualan' => 'Rp ' . number_format($item->total_penjualan, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->perusahaan->nama_perusahaan . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('total_penjualan')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function viewTotalRekapPenjualanPerusahaan(Request $request)
    {
        $query = RekapPenjualanPerusahaan::query();
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('perusahaan.nama_perusahaan')->map(fn($items) => $items->sum('total_penjualan'));
        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Penjualan', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    // ... (Lakukan hal yang sama untuk semua fungsi lainnya) ...
    // Contoh untuk beberapa fungsi berikutnya:

    public function exportLaporanPaketAdministrasi(Request $request)
    {
        $query = LaporanPaketAdministrasi::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('website')->map(fn($items) => $items->sum('total_paket'));
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('status')->map(fn($items) => $items->sum('total_paket'));
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('instansi')->map(fn($items) => $items->sum('nilai'));
        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanHolding(Request $request)
    {
        $query = LaporanHolding::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Perusahaan' => $item->perusahaan->nama_perusahaan,
            'Nilai' => 'Rp ' .  number_format($item->nilai, 0, ',', '.'),
        ]);

        $labels = $rekap->map(fn($item) => $item->perusahaan->nama_perusahaan . ' - ' . Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $rekap->pluck('nilai')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'rekap' => $formattedData,
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function ChartTotalHolding(Request $request)
    {
        $query = LaporanHolding::query();
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('perusahaan.nama_perusahaan')->map(fn($items) => $items->sum('nilai'));
        $labels = $akumulasiData->keys()->toArray();
        $data = $akumulasiData->values()->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        return [
            'chart' => ['labels' => $labels, 'datasets' => [['label' => 'Total Nilai', 'data' => $data, 'backgroundColor' => $backgroundColors]]],
        ];
    }

    public function exportLaporanStok(Request $request)
    {
        $query = LaporanStok::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_pendapatan'));
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->get();

        if ($rekap->isEmpty()) return [];

        $akumulasiData = $rekap->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_piutang'));
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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

    public function exportCuti(Request $request)
    {
        $query = LaporanCutiDivisi::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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

    public function exportIzin(Request $request)
    {
        $query = LaporanIzinDivisi::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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

    public function exportTerlambat(Request $request)
    {
        $query = LaporanTerlambatDivisi::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

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

    public function exportLabaRugi(Request $request)
    {
        $query = LaporanLabaRugi::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
        if ($rekap->isEmpty()) return [];
        return ['rekap' => $rekap->map(function ($item) {
            $imagePath = public_path('images/it/laporanbizdevgambar/' . $item->gambar);
            return [
                'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
                'Gambar' => file_exists($imagePath) ? asset('images/it/laporanbizdevgambar/' . $item->gambar) : asset('images/no-image.png'),
                'Keterangan' => $item->keterangan,
            ];
        })];
    }

    public function exportKHPS(Request $request)
    {
        $query = KasHutangPiutang::query();
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Kas' => 'Rp ' .  number_format($item->kas, 0, ',', '.'),
            'Hutang' => 'Rp ' .  number_format($item->hutang, 0, ',', '.'),
            'Piutang' => 'Rp ' .  number_format($item->piutang, 0, ',', '.'),
            'Stok' => 'Rp ' .  number_format($item->stok, 0, ',', '.'),
        ]);

        $totalKas = $rekap->sum('kas');
        $totalHutang = $rekap->sum('hutang');
        $totalPiutang = $rekap->sum('piutang');
        $totalStok = $rekap->sum('stok');

        $labels = [
            "Kas : Rp " . number_format($totalKas, 0, ',', '.'),
            "Hutang : Rp " . number_format($totalHutang, 0, ',', '.'),
            "Piutang : Rp " . number_format($totalPiutang, 0, ',', '.'),
            "Stok : Rp " . number_format($totalStok, 0, ',', '.'),
        ];
        $data = [$totalKas, $totalHutang, $totalPiutang, $totalStok];
        
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();

        if ($rekap->isEmpty()) return [];

        $formattedData = $rekap->map(fn($item) => [
            'Tanggal' => Carbon::parse($item->tanggal)->translatedFormat('F Y'),
            'Masuk' => 'Rp ' .  number_format($item->kas_masuk, 0, ',', '.'),
            'Keluar' => 'Rp ' .  number_format($item->kas_keluar, 0, ',', '.'),
        ]);

        $kasMasuk = $rekap->sum('kas_masuk');
        $kasKeluar = $rekap->sum('kas_keluar');

        $labels = [
            "Kas Masuk : Rp " . number_format($kasMasuk, 0, ',', '.'),
            "Kas Keluar : Rp " . number_format($kasKeluar, 0, ',', '.'),
        ];
        $data = [$kasMasuk, $kasKeluar];

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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
        $this->applyDateFilter($query);
        $rekap = $query->orderBy('tanggal', 'asc')->get();
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
