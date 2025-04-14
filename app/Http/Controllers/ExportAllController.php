<?php

namespace App\Http\Controllers;
use Mpdf\Mpdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\Perusahaan;
use Carbon\Month;

class ExportAllController extends Controller
{
    /**
     * Export Rekap Penjualan to PDF using client-side data
     * 
     * 
     */
    private $month;
    private $year;

    function __construct(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
    }

    public function exportRekapPenjualanPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);

            // Ambil data dari request
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);

            // Validasi isi tabel dan chart untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }

            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 10, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);

            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Rekap Penjualan|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_rekap_penjualan.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    /**
     * Export Rekap Penjualan Perusahaan to PDF using client-side data
     */
    public function exportRekapPenjualanPerusahaanPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);

            // Ambil data dari request
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);

            // Validasi isi tabel dan chart untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }

            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 10, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);

            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Rekap Penjualan Perusahaan|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Perusahaan</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan Perusahaan</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_rekap_penjualan_perusahaan.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    /**
     * Server-side rendering for Rekap Penjualan export (optional alternative method)
     */

    public function exportRekapPenjualanServerSide(Request $request)
    {
        


        try {
            // Ambil data rekap penjualan
            $rekappenjualans = RekapPenjualan::query()
        ->where($this->month, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->withQueryString();
        // dd($rekappenjualans);

        // Format tambahan untuk tampilan
        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });

        // Data chart
        function getRandomRGBA2($opacity = 0.7)
        {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }

        $labels = $rekappenjualans->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
        })->toArray();

        $data = $rekappenjualans->pluck('total_penjualan')->toArray();
        $backgroundColors = array_map(fn() => getRandomRGBA2(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'text' => 'Total Penjualan',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
            
            // Render view dengan data
            $view = view('exports.rekap-penjualan', compact('rekappenjualans', 'chartData'))->render();
            
            // Buat PDF dari view
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'format' => 'A4',
            ]);
            
            // $mpdf->WriteHTML($view);
            
            // // Return PDF sebagai download
            // return $mpdf->Output('laporan_rekap_penjualan.pdf', 'D');
            
        } catch (\Exception $e) {
            Log::error('Error rendering PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Server-side rendering for Rekap Penjualan Perusahaan export (optional alternative method)
     */
    public function exportRekapPenjualanPerusahaanServerSide()
    {
        try {
            // Ambil data rekap penjualan perusahaan
            $rekappenjualanperusahaans = RekapPenjualanPerusahaan::with('perusahaan')
                ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                ->get();
            
            // Siapkan data untuk chart
            function getRandomRGBA1($opacity = 0.7) {
                return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
            }
            
            $labels = $rekappenjualanperusahaans->map(function($item) {
                $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
                return $item->perusahaan->nama_perusahaan.' - ' . $formattedDate;
            })->toArray();
            
            $data = $rekappenjualanperusahaans->pluck('total_penjualan')->toArray();
            $backgroundColors = array_map(fn() => getRandomRGBA1(), $data);
            
            $chartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Grafik Rekap Penjualan Perusahaan',
                        'text' => 'Total Penjualan Perusahaan',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                    ],
                ],
            ];
            
            // Render view dengan data
            $view = view('exports.rekap-penjualan-perusahaan', compact('rekappenjualanperusahaans', 'chartData'))->render();
            
            // Buat PDF dari view
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'format' => 'A4',
            ]);
            
            $mpdf->WriteHTML($view);
            
            // Return PDF sebagai download
            return $mpdf->Output('laporan_rekap_penjualan_perusahaan.pdf', 'D');
            
        } catch (\Exception $e) {
            Log::error('Error rendering PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}