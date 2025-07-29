<?php

namespace App\Http\Controllers;

use App\Models\LaporanPaketAdministrasi;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanPaketAdministrasiController extends Controller
{
    use DateValidationTrait;

    // Menampilkan halaman utama
    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     #$query = KasHutangPiutang::query();

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $laporanpaketadministrasis = LaporanPaketAdministrasi::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);

    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalPenjualan = $laporanpaketadministrasis->sum('total_paket');

    //     // Siapkan data untuk chart
    //     function getRandomRGBA($opacity = 0.7) {
    //         return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //     }
        
    //     $labels = $laporanpaketadministrasis->map(function($item) {
    //         $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //         return $item->website . ' - ' . $formattedDate;
    //     })->toArray();
    //     $data = $laporanpaketadministrasis->pluck('total_paket')->toArray();
        
    //     // Generate random colors for each data item
    //     $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
    //     $chartData = [
    //         'labels' => $labels, // Labels untuk chart
    //         'datasets' => [
    //             [
    //                 'label' => 'Administrative Package Chart', // Nama dataset
    //                 'text' => 'Total Package', // Nama dataset
    //                 'data' => $data, // Data untuk chart
    //                 'backgroundColor' => $backgroundColors, // Warna batang random
    //             ],
    //         ],
    //     ];
    // $aiInsight = null;

    // // 2. Hanya jalankan fungsi AI jika request memiliki parameter 'generate_ai'.
    // if ($request->has('generate_ai')) {
    //     $aiInsight = $this->generateSalesInsight($laporanpaketadministrasis, $chartData);
    // }
        
    //     return view('marketings.laporanpaketadministrasi',  compact('laporanpaketadministrasis', 'chartData','aiInsight'));    
    // }
    public function index(Request $request)
{
    $perPage = $request->input('per_page', 12);
    $search = $request->input('search');

    // Query dasar untuk digunakan kembali
    $baseQuery = LaporanPaketAdministrasi::query()
        ->when($search, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"]));

    // [FIX] Ambil SEMUA data untuk analisis dan chart
    $allReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

    // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
    $laporanpaketadministrasis = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

    // Siapkan data chart dari SEMUA data
    $labels = $allReports->map(function($item) {
        $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
        return $item->website . ' - ' . $formattedDate;
    })->all();
    
    $data = $allReports->pluck('total_paket')->all();
    
    $chartData = [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Administrative Package Chart',
            'text' => 'Total Package',
            'data' => $data,
            'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
        ]],
    ];
    
    $aiInsight = null;
    if ($request->has('generate_ai')) {
        // [FIX] Panggil AI dengan SEMUA data, bukan data terpaginasi
        $aiInsight = $this->generateSalesInsight($allReports, $chartData);
    }
        
    return view('marketings.laporanpaketadministrasi', compact('laporanpaketadministrasis', 'chartData', 'aiInsight'));
    }
    private function getChartTotalData($reports)
    {
        // Akumulasi total paket berdasarkan website
        $akumulasiData = [];
        foreach ($reports as $item) {
            $namaWebsite = $item->website;
            if (!isset($akumulasiData[$namaWebsite])) {
                $akumulasiData[$namaWebsite] = 0;
            }
            $akumulasiData[$namaWebsite] += $item->total_paket;
        }

        // Siapkan data untuk chart
        $labels = array_keys($akumulasiData);
        $data = array_values($akumulasiData);
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Total Paket',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ]],
        ];
    }

    private function generateSalesInsight($reportData, $chartData): string // [FIX] Nama variabel diubah agar lebih jelas
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($reportData->isEmpty()) {
            return 'Tidak ada data laporan yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data
            $analysisData = [
                'periods'        => $chartData['labels'],
                'package_values' => $chartData['datasets'][0]['data'],
                'total_packages' => $reportData->sum('total_paket'),    // Menggunakan 'total_paket'
                'average_package'=> $reportData->avg('total_paket'),     // Menggunakan 'total_paket'
                'max_package'    => $reportData->max('total_paket'),      // Menggunakan 'total_paket'
                'min_package'    => $reportData->min('total_paket'),      // Menggunakan 'total_paket'
                'data_count'     => $reportData->count(),
            ];

            $prompt = $this->createAnalysisPrompt($analysisData);

            // ... (sisa kode pemanggilan API tidak berubah) ...
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$apiUrl}?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 800,
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari AI.';
            }

            Log::error('Gemini API error: ' . $response->body());
            return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * Buat prompt analisis yang dikirim ke AI.
     */
    private function createAnalysisPrompt(array $data): string
    {
        // [FIX] Prompt diubah total agar sesuai konteks
        $periods = implode(', ', $data['periods']);
        $values  = implode(', ', array_map(fn($v) => 'Rp' . number_format($v, 0, ',', '.'), $data['package_values']));
        $total_packages = number_format($data['total_packages'], 0, ',', '.');
        $average_package = number_format($data['average_package'], 0, ',', '.');
        $max_package = number_format($data['max_package'], 0, ',', '.');
        $min_package = number_format($data['min_package'], 0, ',', '.');

        return <<<PROMPT
    Anda adalah seorang analis performa layanan digital dan operasional di sebuah perusahaan teknologi.

    Berikut adalah data rekap nilai "Paket Administrasi" bulanan per website yang dikelola, dalam Rupiah:
    - Periode/Website Data: {$periods}
    - Rincian Nilai Paket per Periode/Website: {$values}
    - Total Nilai Paket Selama Periode: Rp {$total_packages}
    - Rata-rata Nilai Paket: Rp {$average_package}
    - Nilai Paket Tertinggi: Rp {$max_package}
    - Nilai Paket Terendah: Rp {$min_package}
    - Jumlah Data Laporan: {$data['data_count']}

    Tugas Anda adalah membuat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan profesional untuk manajer operasional. Laporan harus mencakup:
    1. **Ringkasan Kinerja:** Jelaskan secara singkat tren nilai paket administrasi. Apakah ada website yang menonjol (secara konsisten tinggi atau rendah)?
    2. **Identifikasi Puncak & Penurunan:** Sebutkan website/periode dengan nilai paket tertinggi dan terendah. Berikan kemungkinan interpretasi bisnis dari data ini (misalnya, nilai tinggi mungkin karena ada proyek setup baru, nilai rendah karena website dalam mode maintenance).
    3. **Rekomendasi Operasional:** Berdasarkan data, berikan 2-3 poin rekomendasi. Contoh: 'Website X memiliki nilai paket tertinggi, perlu dianalisis apakah ini karena efisiensi atau ada biaya tak terduga.' atau 'Standarisasi paket layanan berdasarkan performa Website Y bisa dipertimbangkan untuk efisiensi biaya.'
    4. **Alokasi Sumber Daya:** Berikan saran singkat tentang bagaimana data ini bisa digunakan untuk merencanakan alokasi sumber daya atau budget untuk periode berikutnya.

    Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.
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
                'website' => [
                    'required',
                    Rule::in([
                        'E - Katalog',
                        'E - Katalog Luar Bali',
                        'Balimall',
                        'Siplah',
                        'PL',
                    ]),
                ],
                'total_paket' => 'required|integer|min:0',
            ]);
            
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan website
            $exists = LaporanPaketAdministrasi::where('tanggal', $validatedData['tanggal'])
            ->where('website', $validatedData['website'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            LaporanPaketAdministrasi::create($validatedData);
            return redirect()->route('laporanpaketadministrasi.index')->with('success', 'Data Berhasil Ditambahkan');

        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            Log::info('website input:', [$request->input('website')]);
            return redirect()->route('laporanpaketadministrasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanPaketAdministrasi $laporanpaketadministrasi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'website' => [
                'required',
                Rule::in([
                    'E - Katalog',
                    'E - Katalog Luar Bali',
                    'Balimall',
                    'Siplah',
                    'PL',
                ]),
            ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Cek kombinasi unik date dan website
            $exists = LaporanPaketAdministrasi::where('tanggal', $validatedData['tanggal'])
            ->where('website', $validatedData['website'])
            ->where('id_laporanpaket', '!=', $laporanpaketadministrasi->id_laporanpaket)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Tidak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $laporanpaketadministrasi->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanpaketadministrasi.index')
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
                ->route('laporanpaketadministrasi.index')
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
    
            // Ambil data dari request dan trim spasi
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);
    
            // Validasi isi tabel dan grafik untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }
    
            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 35, // Tambahkan margin atas untuk header
                'margin_bottom' => 10, // Margin bawah
                'format'        => 'A4', // Ukuran kertas A4
            ]);
    
            // Siapkan header image jika file ada
            $headerImagePath = public_path('images/HEADER.png');
            if (file_exists($headerImagePath)) {
                $headerHtml = "
                    <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                        <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                    </div>
                ";
                $mpdf->SetHTMLHeader($headerHtml, 'O'); // Untuk halaman pertama dan seterusnya
            } else {
                Log::warning("Header image tidak ditemukan di path: {$headerImagePath}");
            }
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Administrative Package Report|');
    
            // Buat konten HTML dengan styling CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Website</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Value Package</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Administrative Package Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
    
            // Tulis konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Ambil output PDF sebagai string
            $pdfOutput = $mpdf->Output('', 'S');
    
            // Kembalikan PDF sebagai response download
            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_paket_administrasi.pdf"');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    public function destroy(LaporanPaketAdministrasi $laporanpaketadministrasi)
    {
        try {
            $laporanpaketadministrasi->delete();
            return redirect()->route('laporanpaketadministrasi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporanpaketadministrasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    {
    // Ambil data dari database
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    $query = LaporanPaketAdministrasi::query();

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
    
    $laporanpaketadministrasis = $query
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->get();

    // Siapkan data untuk chart
    $labels = $laporanpaketadministrasis->pluck('website')->toArray();
    $data = $laporanpaketadministrasis->pluck('total_paket')->toArray();
    $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Total Paket',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ],
        ],
    ];

    // Kembalikan data dalam format JSON
    return response()->json($chartData);
}
private function getRandomRGBAA($opacity = 0.7)
{
    return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
}

public function chartTotal(Request $request)
    {
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    // Ambil data dari database
    $query = LaporanPaketAdministrasi::query();
    
    // Filter berdasarkan tanggal jika ada
    if ($search) {
        $query->where('tanggal', 'LIKE', "%$search%");
    }
    
    // Filter berdasarkan range bulan-tahun jika keduanya diisi
    if ($startMonth && $endMonth) {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
        
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
    
    $laporanpaketadministrasis = $query->get();

    // Akumulasi total penjualan berdasarkan nama website
    $akumulasiData = [];
    foreach ($laporanpaketadministrasis as $item) {
        $namaWebsite = $item->website;
        if (!isset($akumulasiData[$namaWebsite])) {
            $akumulasiData[$namaWebsite] = 0;
        }
        $akumulasiData[$namaWebsite] += $item->total_paket;
    }

    // Siapkan data untuk chart
    $labels = array_keys($akumulasiData);
    $data = array_values($akumulasiData);
    $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);

    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Total Paket',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ],
        ],
    ];

    // Kembalikan data dalam format JSON
    return response()->json($chartData);
}

private function getRandomRGBAA1($opacity = 0.7)
{
    return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
}
}

