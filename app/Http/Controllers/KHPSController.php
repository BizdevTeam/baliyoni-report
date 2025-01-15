<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasHutangPiutang;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class KHPSController extends Controller
{
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query untuk mencari berdasarkan tahun dan bulan
        $kashutangpiutangstoks = KasHutangPiutang::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalKas = $kashutangpiutangstoks->sum('kas');
        $totalHutang = $kashutangpiutangstoks->sum('hutang');
        $totalPiutang = $kashutangpiutangstoks->sum('piutang');
        $totalStok = $kashutangpiutangstoks->sum('stok');

        // Siapkan data untuk chart
        $chartData = [
            'labels' => ['Kas', 'Hutang', 'Piutang', 'Stok'],
            'datasets' => [
                [
                    'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                ],
            ],
        ];
        return view('accounting.khps', compact('kashutangpiutangstoks', 'chartData'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = KasHutangPiutang::where('bulan', $validatedata['bulan'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            KasHutangPiutang::create($validatedata);
    
            return redirect()->route('khps.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, KasHutangPiutang $khp)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = KasHutangPiutang::where('bulan', $validatedata['bulan'])
                ->where('id_khps', '!=', $khp->id_khps)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            $khp->update($validatedata);
    
            return redirect()->route('khps.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Kas Hutang Piutang Stok|Halaman {PAGENO}');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $tableHTMLContent = "
                <h1 style='text-align:center; font-size: 16px; margin-top: 32px;'>Laporan Kas Hutang Piutang Stok</h1>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Data Rekapitulasi</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Bulan/Tahun</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Kas (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Hutang (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Piutang (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Stok (Rp)</th>
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
                    <h1 style='text-align:center; font-size: 16px; margin: 10px 0;'>Grafik Kas Hutang Piutang Stok</h1>
                    <div style='text-align: center; margin: 10px 0;'>
                        <img src='{$chartBase64}' alt='Chart' style='max-width: 50%; height: auto;' />
                    </div>
                ";
                $mpdf->WriteHTML($chartHTMLContent);
            }

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_kas_hutang_piutang_stok.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }


    public function destroy(KasHutangPiutang $khp)
    {
        try {
            $khp->delete();

            return redirect()->route('khps.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getKashutangpiutangstokData()
    {
        $data = KasHutangPiutang::all(['kas', 'hutang', 'piutang', 'stok']);
    
        return response()->json($data);
    }

}

