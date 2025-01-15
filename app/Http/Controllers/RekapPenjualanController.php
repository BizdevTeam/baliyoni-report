<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPenjualan;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RekapPenjualanController extends Controller
{
    // Show the view
    public function index(Request $request)
    { 
        $perekappenjualanage = $request->input('per_page', 5);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $rekappenjualans = RekapPenjualan::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perekappenjualanage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $rekappenjualans->sum('total_penjualan');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $rekappenjualans->pluck('bulan')->toArray();
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Rekap Penjualan', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('marketings.rekappenjualan', compact('rekappenjualans', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_penjualan' => 'required|integer|min:0',
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = RekapPenjualan::where('bulan', $validatedata['bulan'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            RekapPenjualan::create($validatedata);
    
            return redirect()->route('rekappenjualan.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('rekappenjualan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPenjualan $rekappenjualan)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_penjualan' => 'required|integer|min:0',
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = RekapPenjualan::where('bulan', $validatedData['bulan'])
                ->where('id_rp', '!=', $rekappenjualan->id_rp)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $rekappenjualan->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('rekappenjualan.index')->with('success', 'Data berhasil diperbarui.');
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
                ->route('rekappenjualan.index')
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
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Rekap Penjualan|Halaman {PAGENO}');

        // Buat konten tabel dengan gaya CSS yang lebih ketat
        $tableHTMLContent = "
            <h1 style='text-align:center; font-size: 16px; margin-top: 50px;'>Laporan Rekap Penjualan</h1>
            <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Data Rekapitulasi</h2>
            <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #000; padding: 5px;'>Bulan/Tahun</th>
                        <th style='border: 1px solid #000; padding: 5px;'>Total Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    {$tableHTML}
                </tbody>
            </table>
        ";

        // Tambahkan konten tabel ke PDF
        $mpdf->WriteHTML($tableHTMLContent);

        // Tambahkan pemisah halaman
        $mpdf->AddPage();

        // Tambahkan halaman baru dengan konten grafik
        if (!empty($chartBase64)) {
            $chartHTMLContent = "
                <h1 style='text-align:center; font-size: 16px; margin: 10px 0;'>Grafik Rekap Penjualan</h1>
                <div style='text-align: center; margin: 10px 0;'>
                    <img src='{$chartBase64}' alt='Chart' style='max-width: 90%; height: auto;' />
                </div>
            ";
            $mpdf->WriteHTML($chartHTMLContent);
        }

        // Return PDF sebagai respon download
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan.pdf"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}


    public function destroy(RekapPenjualan $rekappenjualan)
    {
        try {
            $rekappenjualan->delete();
            return redirect()->route('rekappenjualan.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('rekappenjualan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getRekapPenjualanData()
    {
        $data = RekapPenjualan::all(['bulan','total_penjualan']);
    
        return response()->json($data);
    }

}

