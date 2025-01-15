<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanDetrans;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;


class LaporanDetransController extends Controller
{
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $laporandetrans = LaporanDetrans::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporandetrans->sum('total_pengiriman');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporandetrans->pluck('bulan')->toArray();
        $data = $laporandetrans->pluck('total_pengiriman')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        $borderColors = array_map(fn() => getRandomRGBA(1.0), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Laporan Pengiriman Detrans', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('supports.laporandetrans', compact('laporandetrans', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pengiriman' => 'required|integer|min:0',
            ]);
    
            LaporanDetrans::create($validatedata);
    
            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Detrans Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanDetrans $laporandetran)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pengiriman' => 'required|integer|min:0',
            ]);
    
            // Update data
            $laporandetran->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporandetrans.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Detrans Data: ' . $e->getMessage());
            return redirect()
                ->route('laporandetrans.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function exportPDF(Request $request)
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
            'margin_top' => 35, // Kurangi margin atas
            'margin_bottom' => 10, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);

        // Tambahkan header ke PDF
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Pengiriman Detrans|Halaman {PAGENO}');

        // Konten HTML
        $htmlContent = "
        <div style='display: flex; flex-direction: row; align-items: flex-start; gap: 20px;'>
            <div style='width: 30%;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Data Rekapitulasi</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Bulan</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
            <div style='width: 25%; text-align: center;'>
                <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Penjualan</h2>
                <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Penjualan' />
            </div>
        </div>
        ";
        // Tambahkan konten ke PDF
        $mpdf->WriteHTML($htmlContent);

        // Return PDF sebagai respon download
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_pengiriman_detrans.pdf"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}


    public function destroy(LaporanDetrans $laporandetran)
    {
        try {
            $laporandetran->delete();
            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Detrans Data Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanSamitraData()
    {
        $data = LaporanDetrans::all(['bulan','total_pengiriman']);
    
        return response()->json($data);
    }

}

