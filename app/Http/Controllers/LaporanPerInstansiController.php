<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanPerInstansiController extends Controller
{
    // Menampilkan halaman utama
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $laporanperinstansis = LaporanPerInstansi::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporanperinstansis->sum('nilai');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporanperinstansis->pluck('bulan')->toArray();
        $data = $laporanperinstansis->pluck('nilai')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        $borderColors = array_map(fn() => getRandomRGBA(1.0), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Total Penjualan', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                    'borderColor' => $borderColors,        // Warna border random
                    'borderWidth' => 1,                    // Ketebalan border
                ],
            ],
        ];
        
        return view('marketings.laporanperinstansi', compact('laporanperinstansis', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'instansi' => [
                    'required',
                    Rule::in([
                        'Badung',
                        'Denpasar',
                        'Provinsi',
                        'Bangli',
                        'Klungkung',
                    ]),
                ],
                'nilai' => 'required|integer|min:0',
            ]);
    
            LaporanPerInstansi::create($validatedata);
    
            return redirect()->route('laporanperinstansi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Status Paket: ' . $e->getMessage());
            Log::info('Perusahaan input:', [$request->input('instansi')]);
            return redirect()->route('laporanperinstansi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanPerInstansi $laporanperinstansi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'instansi' => [
                'required',
                Rule::in([
                    'Badung',
                    'Denpasar',
                    'Provinsi',
                    'Bangli',
                    'Klungkung',
                ]),
            ],
                'nilai' => 'required|integer|min:0',
            ]);
    
            // Update data
            $laporanperinstansi->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanperinstansi.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Status Paket: ' . $e->getMessage());
            return redirect()
                ->route('laporanperinstansi.index')
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
            'margin_top' => 10, // Kurangi margin atas
            'margin_bottom' => 10, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);

        // Tambahkan header ke PDF
        $mpdf->SetHeader('Laporan Per Instansi||{PAGENO}');

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Rekap Penjualan|Halaman {PAGENO}');

        // Buat konten tabel dengan gaya CSS yang lebih ketat
        $tableHTMLContent = "
            <h1 style='text-align:center; font-size: 16px; margin-top: 32px;'>Laporan Per Instansi</h1>
            <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Data Rekapitulasi</h2>
            <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #000; padding: 5px;'>Bulan/Tahun</th>
                        <th style='border: 1px solid #000; padding: 5px;'>Instansi</th>
                        <th style='border: 1px solid #000; padding: 5px;'>Nilai</th>
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
                <h1 style='text-align:center; font-size: 16px; margin: 10px 0;'>Grafik Rekap Penjualan Perusahaan</h1>
                <div style='text-align: center; margin: 10px 0;'>
                    <img src='{$chartBase64}' alt='Chart' style='max-width: 50%; height: auto;' />
                </div>
            ";
            $mpdf->WriteHTML($chartHTMLContent);
        }

        // Return PDF sebagai respon download
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_per_instansi.pdf"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}

    public function destroy(LaporanPerInstansi $laporanperinstansi)
    {
        try {
            $laporanperinstansi->delete();
            return redirect()->route('laporanperinstansi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Status Paket: ' . $e->getMessage());
            return redirect()->route('laporanperinstansi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanPaketAdministrasiData()
    {
        $data = LaporanPerInstansi::all(['bulan','instansi','nilai']);
    
        return response()->json($data);
    }

}

