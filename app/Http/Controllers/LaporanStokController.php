<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanStok;
use App\Traits\DateValidationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;

class LaporanStokController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan date
        $laporanstoks = LaporanStok::query()
            ->when($search, function ($query, $search) {
                return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporanstoks->sum('stok');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporanstoks->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $formattedDate;
        })->toArray();
        $data = $laporanstoks->pluck('stok')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'text' => 'Total Nilai Stok', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
         $aiInsight = null;

    // 2. Hanya jalankan fungsi AI jika request memiliki parameter 'generate_ai'.
    if ($request->has('generate_ai')) {
        $aiInsight = $this->generateSalesInsight($laporanstoks, $chartData);
    }   
        return view('procurements.laporanstok', compact('laporanstoks', 'chartData'));    }

    private function generateSalesInsight($stockData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($stockData->isEmpty()) {
            return 'Tidak ada data stok yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data "Laporan Stok"
            $analysisData = [
                'periods'       => $chartData['labels'],
                'stock_values'  => $chartData['datasets'][0]['data'],
                'total_stock'   => $stockData->sum('stok'),    // Menggunakan 'stok'
                'average_stock' => $stockData->avg('stok'),     // Menggunakan 'stok'
                'max_stock'     => $stockData->max('stok'),      // Menggunakan 'stok'
                'min_stock'     => $stockData->min('stok'),      // Menggunakan 'stok'
                'data_count'    => $stockData->count(),
            ];
            
            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createStockAnalysisPrompt($analysisData);

            // ... sisa kode pemanggilan API tidak berubah ...
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [ 'temperature' => 0.7, 'maxOutputTokens' => 800 ],
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
            }

            Log::error('Gemini API error: ' . $response->body());
            return 'Gagal menghubungi layanan analisis AI.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Stok.
     */
    private function createStockAnalysisPrompt(array $data): string
    {
        $periods = implode(', ', $data['periods']);
        $values  = implode(', ', array_map(fn($v) => number_format($v, 0, ',', '.'), $data['stock_values'])); // Stok mungkin tidak dalam Rupiah
        $total_stock = number_format($data['total_stock'], 0, ',', '.');
        $average_stock = number_format($data['average_stock'], 0, ',', '.');
        $max_stock = number_format($data['max_stock'], 0, ',', '.');
        $min_stock = number_format($data['min_stock'], 0, ',', '.');

        return <<<PROMPT
    Anda adalah seorang Manajer Gudang dan Logistik yang berpengalaman.

    Berikut adalah data rekapitulasi nilai total stok (bisa dalam unit atau nilai moneter) per periode waktu.
    - Periode Data: {$periods}
    - Rincian Nilai Stok per Periode: {$values}

    **Ringkasan Statistik Stok:**
    - Total Akumulasi Stok: {$total_stock}
    - Rata-rata Nilai Stok per Periode: {$average_stock}
    - Nilai Stok Tertinggi dalam Satu Periode: {$max_stock}
    - Nilai Stok Terendah dalam Satu Periode: {$min_stock}
    - Jumlah Periode Data: {$data['data_count']}

    **Tugas Anda:**
    Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk manajer logistik atau pengadaan (procurement).

    Analisis harus mencakup:
    1.  **Ringkasan Kondisi Stok:** Jelaskan tren umum dari nilai stok. Apakah stok cenderung menumpuk (naik), menipis (turun), atau stabil?
    2.  **Identifikasi Puncak & Penurunan Stok:** Sebutkan periode dengan stok tertinggi (potensi overstock) dan terendah (potensi stockout). Berikan hipotesis penyebabnya (misal: "Stok memuncak pada bulan Desember karena persiapan liburan akhir tahun," atau "Stok menipis di bulan Maret, kemungkinan karena keterlambatan pengiriman dari pemasok.").
    3.  **Rekomendasi Manajemen Inventaris:** Berikan 2-3 poin rekomendasi konkret. Contoh: 'Pertimbangkan untuk melakukan 'stock opname' pada periode setelah stok puncak untuk validasi data.' atau 'Tinjau 'safety stock level' untuk mencegah level stok turun serendah yang terjadi pada periode X.'
    4.  **Proyeksi Kebutuhan:** Berdasarkan tren, berikan saran singkat mengenai perencanaan pengadaan (procurement) untuk periode berikutnya. Contoh: 'Melihat tren kenaikan, disarankan untuk meningkatkan volume pemesanan sebesar 10% untuk kuartal berikutnya.'

    Gunakan format markdown untuk poin-poin agar mudah dibaca.
    PROMPT;
    }

    private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'stok' => 'required|integer|min:0'
            ]);
            
            $errorMessage = '';
                if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                    return redirect()->back()->with('error', $errorMessage);
                }
            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanStok::where('tanggal', $validatedData['tanggal'])->exists();
                    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanStok::create($validatedData);
    
            return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Stok data: ' . $e->getMessage());
            return redirect()->route('laporanstok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanStok $laporanstok)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'stok' => 'required|integer|min:0',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = LaporanStok::where('tanggal', $validatedData['tanggal'])
                ->where('id_stok', '!=', $laporanstok->id_stok)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $laporanstok->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanstok.index')
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
                ->route('laporanstok.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Stock Report|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Stock Value(Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Stock Chart Report</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_stok.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanStok $laporanstok)
    {
        try {
            $laporanstok->delete();
            return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Stok: ' . $e->getMessage());
            return redirect()->route('laporanstok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanStokData()
    {
        $data = LaporanStok::all(['tanggal','stok']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = LaporanStok::query();
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
        
        $laporanstoks = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();
    
        // Format tanggal menjadi "F Y" (contoh: "March 2025")
        $labels = $laporanstoks->map(fn($item) => Carbon::parse($item->tanggal)->translatedFormat('F Y'))->toArray();
        $data = $laporanstoks->pluck('stok')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);
    
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nilai Paket',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ]);
    }

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

}

