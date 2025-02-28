<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class ArusKasController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query untuk mencari berdasarkan tahun dan date
        $aruskass = ArusKas::query()
            ->when($search, function ($query, $search) {
                return $query->where('date', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(date) DESC, MONTH(date) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalKas = $aruskass->sum('kas_masuk');
        $totalHutang = $aruskass->sum('kas_keluar');

        // Siapkan data untuk chart
        $chartData = [
            'labels' => ['Kas Masuk', 'Kas Keluar'],
            'datasets' => [
                [
                    'data' => [$totalKas, $totalHutang],
                    'backgroundColor' => ['#FF6384', '#36A2EB'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB'],
                ],
            ],
        ];
        return view('accounting.aruskas', compact('aruskass', 'chartData'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = ArusKas::where('date', $validatedData['date'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            ArusKas::create($validatedData);

            return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error updating Arus Kas: ' . $e->getMessage());
            return redirect()->route('aruskas.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ArusKas $aruska)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = ArusKas::where('date', $validatedData['date'])
                ->where('id_aruskas', '!=', $aruska->id_aruskas)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            $aruska->update($validatedData);
    
            return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Arus Kas data: ' . $e->getMessage());
            return redirect()->route('aruskas.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Laporan Arus Kas|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='gap: 100px; width: 100%;'>
                <div style='width: 45%; float: left; padding-right: 20px;'>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Kas Masuk (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Kas Keluar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
                        </div>
                <div style='width: 45%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Arus Kas</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_arus_kas.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }


    public function destroy(ArusKas $aruska)
    {
        try {
            $aruska->delete();
            return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Arus Kas data: ' . $e->getMessage());
            return redirect()->route('aruskas.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    { 
        $search = $request->input('search');

        // Query untuk mencari berdasarkan tahun dan date
        $aruskass = ArusKas::query()
            ->when($search, function ($query, $search) {
                return $query->where('date', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(date) DESC, MONTH(date) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->get();

        // Hitung total untuk masing-masing kategori
        $totalKas = $aruskass->sum('kas_masuk');
        $totalHutang = $aruskass->sum('kas_keluar');

        // Siapkan data untuk chart
        $chartData = [
            'labels' => ['Kas Masuk', 'Kas Keluar'],
            'datasets' => [
                [
                    'data' => [$totalKas, $totalHutang],
                    'backgroundColor' => ['#FF6384', '#36A2EB'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB'],
                ],
            ],
        ];
        return response()->json($chartData);
    }
}
