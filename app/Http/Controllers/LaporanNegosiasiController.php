<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNegosiasi;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;

class LaporanNegosiasiController extends Controller
{
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $laporannegosiasis = LaporanNegosiasi::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporannegosiasis->sum('total_negosiasi');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporannegosiasis->pluck('bulan')->toArray();
        $data = $laporannegosiasis->pluck('total_negosiasi')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Laporan Negosiasi', // Nama dataset
                    'text' => 'Total Negosiasi', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('procurements.laporannegosiasi', compact('laporannegosiasis', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_negosiasi' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanNegosiasi::where('bulan', $validatedata['bulan'])->exists();
        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanNegosiasi::create($validatedata);
    
            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            Log::error('Error Storing Negosiasi data: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanNegosiasi $laporannegosiasi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_negosiasi' => 'required|integer|min:0',
            ]);

            $exists = LaporanNegosiasi::where('bulan', $validatedData['bulan'])
            ->where('id_negosiasi', '!=', $laporannegosiasi->id_negosiasi)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $laporannegosiasi->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporannegosiasi.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Stok: ' . $e->getMessage());
            return redirect()
                ->route('laporannegosiasi.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Negosiasi|Halaman {PAGENO}');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $tableHTMLContent = "
                <h1 style='text-align:center; font-size: 16px; margin-top: 50px;'>Laporan Negosiasi</h1>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Data Rekapitulasi</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Bulan/Tahun</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Total Negosiasi (Rp)</th>
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
                    <h1 style='text-align:center; font-size: 16px; margin: 10px 0;'>Grafik Laporan Negosiasi</h1>
                    <div style='text-align: center; margin: 10px 0;'>
                        <img src='{$chartBase64}' alt='Chart' style='max-width: 90%; height: auto;' />
                    </div>
                ";
                $mpdf->WriteHTML($chartHTMLContent);
            }

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_negosiasi.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanNegosiasi $laporannegosiasi)
    {
        try {
            $laporannegosiasi->delete();
            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Stok: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanNegosiasiData()
    {
        $data = LaporanNegosiasi::all(['bulan','total_negosiasi']);
    
        return response()->json($data);
    }

}
