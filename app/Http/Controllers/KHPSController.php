<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasHutangPiutang;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class KHPSController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query untuk mencari berdasarkan tahun dan date
        $kashutangpiutangstoks = KasHutangPiutang::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalKas = $kashutangpiutangstoks->sum('kas');
        $totalHutang = $kashutangpiutangstoks->sum('hutang');
        $totalPiutang = $kashutangpiutangstoks->sum('piutang');
        $totalStok = $kashutangpiutangstoks->sum('stok');

        // Format angka menjadi format rupiah atau format angka biasa
        $formattedKas = number_format($totalKas, 0, ',', '.');
        $formattedHutang = number_format($totalHutang, 0, ',', '.');
        $formattedPiutang = number_format($totalPiutang, 0, ',', '.');
        $formattedStok = number_format($totalStok, 0, ',', '.');

        $chartData = [
            'labels' => [
                "Kas : Rp $formattedKas",
                "Hutang : Rp $formattedHutang",
                "Piutang : Rp $formattedPiutang",
                "Stok : Rp $formattedStok",
            ],
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
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = KasHutangPiutang::where('tanggal', $validatedData['tanggal'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            KasHutangPiutang::create($validatedData);
    
            return redirect()->route('khps.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, KasHutangPiutang $khp)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = KasHutangPiutang::where('tanggal', $validatedData['tanggal'])
                ->where('id_khps', '!=', $khp->id_khps)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
            }
    
            $khp->update($validatedData);
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Laporan Kas Hutang Piutang Stok|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='gap: 100px; width: 100%;'>
                <div style='width: 45%; float: left; padding-right: 20px;'>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Tanggal</th>
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
                        </div>
                <div style='width: 45%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Kas Hutang Piutang Stok</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
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
    public function showChart(Request $request)
    { 
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = KasHutangPiutang::query();
            // Filter berdasarkan tanggal jika ada
            if ($search) {
                $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
            }
        
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if ($startMonth && $endMonth) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
            
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        
        $kashutangpiutangstoks = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Hitung total untuk masing-masing kategori
        $totalKas = $kashutangpiutangstoks->sum('kas');
        $totalHutang = $kashutangpiutangstoks->sum('hutang');
        $totalPiutang = $kashutangpiutangstoks->sum('piutang');
        $totalStok = $kashutangpiutangstoks->sum('stok');

        // Format angka menjadi format rupiah atau format angka biasa
        $formattedKas = number_format($totalKas, 0, ',', '.');
        $formattedHutang = number_format($totalHutang, 0, ',', '.');
        $formattedPiutang = number_format($totalPiutang, 0, ',', '.');
        $formattedStok = number_format($totalStok, 0, ',', '.');

        $chartData = [
            'labels' => [
                "Kas : Rp $formattedKas",
                "Hutang : Rp $formattedHutang",
                "Piutang : Rp $formattedPiutang",
                "Stok : Rp $formattedStok",
            ],
            'datasets' => [
                [
                    'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                ],
            ],
        ];
        return response()->json($chartData);
    }

}

