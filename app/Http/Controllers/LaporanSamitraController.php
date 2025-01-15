<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\laporansamitra;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;


class LaporanSamitraController extends Controller
{
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input(  'search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $laporansamitras = LaporanSamitra::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporansamitras->sum('total_pengiriman');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporansamitras->pluck('bulan')->toArray();
        $data = $laporansamitras->pluck('total_pengiriman')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        $borderColors = array_map(fn() => getRandomRGBA(1.0), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Laporan Pengiriman Samitra', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                    'borderColor' => $borderColors,        // Warna border random
                    'borderWidth' => 1,                    // Ketebalan border
                ],
            ],
        ];
        
        return view('supports.laporansamitra', compact('laporansamitras', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pengiriman' => 'required|integer|min:0',
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = laporansamitra::where('bulan', $validatedata['bulan'])->exists();
                
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanSamitra::create($validatedata);
    
            return redirect()->route('laporansamitra.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporansamitra.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSamitra $laporansamitra)
    {
        try {
            // Validasi input
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pengiriman' => 'required|integer|min:0',
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = laporansamitra::where('bulan', $validatedata['bulan'])->exists();
                
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            // Update data
            $laporansamitra->update($validatedata);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporansamitra.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()
                ->route('laporansamitra.index')
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
            'margin_top' => 14, // Kurangi margin atas
            'margin_bottom' => 10, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);

        // Tambahkan header ke PDF
        $mpdf->SetHeader('Laporan Pengiriman Samitra||{PAGENO}');

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Pengiriman Samitra|Halaman {PAGENO}');

        // Buat konten tabel dengan gaya CSS yang lebih ketat
        $tableHTMLContent = "
            <h1 style='text-align:center; font-size: 16px; margin-top: 32px;'>Laporan Pengiriman Samitra</h1>
            <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Data Rekapitulasi</h2>
            <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #000; padding: 5px;'>Bulan/Tahun</th>
                        <th style='border: 1px solid #000; padding: 5px;'>Total Pengiriman (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    {$tableHTML}
                </tbody>
            </table>
        ";

        // Tambahkan konten tabel ke PDF
        $mpdf->WriteHTML($tableHTMLContent);

        // Tambahkan halaman baru hanya jika konten chart tersedia
        if (!empty($chartBase64)) {
            $chartHTMLContent = "
                <h1 style='text-align:center; font-size: 16px; margin: 10px 0;'>Grafik Laporan Pengiriman Samitra</h1>
                <div style='text-align: center; margin: 10px 0;'>
                    <img src='{$chartBase64}' alt='Chart' style='max-width: 90%; height: auto;' />
                </div>
            ";
            $mpdf->WriteHTML($chartHTMLContent);
        }

        // Return PDF sebagai respon download
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_pengiriman_samitra.pdf"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}


    public function destroy(LaporanSamitra $laporansamitra)
    {
        try {
            $laporansamitra->delete();
            return redirect()->route('laporansamitra.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporansamitra.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanSamitraData()
    {
        $data = LaporanSamitra::all(['bulan','total_pengiriman']);
    
        return response()->json($data);
    }

}

