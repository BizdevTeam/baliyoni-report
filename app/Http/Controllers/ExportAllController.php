<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;

class ExportAllController extends Controller
{
    public function exportAll(Request $request)
    {
        try {
            // Validasi input dengan membuat beberapa field optional
            $data = $request->validate([
                //table & chart rekap penjualan
                'table1' => 'nullable|string',
                'chart1' => 'nullable|string',

                //table & chart rekap penjualan perusahaan
                'table2' => 'nullable|string',
                'chart2' => 'nullable|string',
                
                // Optional date range
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            // Inisialisasi variabel dengan string kosong jika null
            $tableHTML1 = $data['table1'] ?? '';
            $chartBase64_1 = $data['chart1'] ?? '';
            $tableHTML2 = $data['table2'] ?? '';
            $chartBase64_2 = $data['chart2'] ?? '';

            // Jika semua data kosong, kembalikan error
            if (empty($tableHTML1) && empty($chartBase64_1) && 
                empty($tableHTML2) && empty($chartBase64_2)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk diekspor.'], 400);
            }

            // Buat instance mPDF dengan konfigurasi
            $mpdf = new Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, 
                'margin_bottom' => 10, 
                'format' => 'A4', 
            ]);

            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png');
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O');

            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Rekap Penjualan|');

            // Buat konten gabungan dengan dua laporan
            $htmlContent = $this->generatePDFContent($tableHTML1, $chartBase64_1, $tableHTML2, $chartBase64_2);
            
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_kombinasi.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting combined PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()], 500);
        }
    }

    private function generatePDFContent($tableHTML1, $chartBase64_1, $tableHTML2, $chartBase64_2)
    {
        $content = "<div style='width: 100%;'>";

        // Laporan Pertama (jika ada data)
        if (!empty($tableHTML1) && !empty($chartBase64_1)) {
            $content .= "
            <div style='width: 100%; margin-bottom: 20px;'>
                <h2 style='font-size: 16px; text-align: center; margin-bottom: 15px;'>Laporan Rekap Penjualan</h2>
                <div style='display: flex; justify-content: space-between;'>
                    <div style='width: 30%; padding-right: 20px;'>
                        <h3 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h3>
                        <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                            <thead>
                                <tr style='background-color: #f2f2f2;'>
                                    <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                    <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$tableHTML1}
                            </tbody>
                        </table>
                    </div>
                    <div style='width: 65%; text-align:center;'>
                        <h3 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan</h3>
                        <img src='{$chartBase64_1}' style='width: 100%; height: auto;' alt='Grafik Laporan 1' />
                    </div>
                </div>
            </div>";
        }

        // Laporan Kedua (jika ada data)
        if (!empty($tableHTML2) && !empty($chartBase64_2)) {
            $content .= "
            <div style='width: 100%;'>
                <h2 style='font-size: 16px; text-align: center; margin-bottom: 15px;'>Laporan Rekap Penjualan Perusahaan</h2>
                <div style='display: flex; justify-content: space-between;'>
                    <div style='width: 30%; padding-right: 20px;'>
                        <h3 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h3>
                        <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                            <thead>
                                <tr style='background-color: #f2f2f2;'>
                                    <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                    <th style='border: 1px solid #000; padding: 1px;'>Perusahaan</th>
                                    <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$tableHTML2}
                            </tbody>
                        </table>
                    </div>
                    <div style='width: 65%; text-align:center;'>
                        <h3 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan Perusahaan</h3>
                        <img src='{$chartBase64_2}' style='width: 100%; height: auto;' alt='Grafik Laporan 2' />
                    </div>
                </div>
            </div>";
        }

        $content .= "</div>";
        return $content;
    }
}