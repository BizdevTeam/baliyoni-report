<?php
namespace App\Http\Controllers;

use App\Models\StatusPaket;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class StatusPaketController extends Controller
{
    use DateValidationTrait;

    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     #$query = KasHutangPiutang::query();

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $statuspakets = StatusPaket::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalPenjualan = $statuspakets->sum('total_paket');

    //     // Siapkan data untuk chart
    //     function getRandomRGBA($opacity = 0.7) {
    //         return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //     }
        
    //     $labels = $statuspakets->map(function($item) {
    //         $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
    //         return $item->status . ' - ' . $formattedDate;
    //     })->toArray();
    //     $data = $statuspakets->pluck('total_paket')->toArray();
        
    //     // Generate random colors for each data item
    //     $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
    //     $chartData = [
    //         'labels' => $labels, // Labels untuk chart
    //         'datasets' => [
    //             [
    //                 'label' => 'Package Status Report', // Nama dataset
    //                 'text' => 'Total Package', // Nama dataset
    //                 'data' => $data, // Data untuk chart
    //                 'backgroundColor' => $backgroundColors, // Warna batang random
    //             ],
    //         ],
    //     ];
    //     $aiInsight = null;
    //         if ($request->has('generate_ai')) {
    //     $aiInsight = $this->generateSalesInsight($statuspakets, $chartData);
    // }
    //    return view('marketings.statuspaket', compact('statuspakets', 'chartData'));    }
    // private function generateSalesInsight($salesData, $chartData)
    // {
    //     // Ambil konfigurasi dari file config/services.php
    //     $apiKey = config('services.gemini.api_key');
    //     $apiUrl = config('services.gemini.api_url');

    //     if (!$apiKey || !$apiUrl) {
    //         Log::error('Gemini API Key or URL is not configured.');
    //         return 'Layanan AI tidak terkonfigurasi dengan benar.';
    //     }
        
    //     // Jangan panggil AI jika tidak ada data untuk dianalisis
    //     if ($salesData->isEmpty()) {
    //         return 'Tidak ada data penjualan yang cukup untuk dianalisis.';
    //     }

    //     try {
    //         $analysisData = [
    //             'periods' => $chartData['labels'],
    //             'sales_values' => $chartData['datasets'][0]['data'],
    //             'total_sales' => $salesData->sum('total_penjualan'),
    //             'average_sales' => $salesData->avg('total_penjualan'),
    //             'max_sales' => $salesData->max('total_penjualan'),
    //             'min_sales' => $salesData->min('total_penjualan'),
    //             'data_count' => $salesData->count(),
    //         ];
            
    //         $prompt = $this->createAnalysisPrompt($analysisData);
            
    //         // Kirim request ke API Gemini dengan format yang BENAR
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //         ])->post($apiUrl . '?key=' . $apiKey, [
    //             'contents' => [
    //                 [
    //                     'parts' => [
    //                         ['text' => $prompt]
    //                     ]
    //                 ]
    //             ],
    //             'generationConfig' => [
    //                 'temperature' => 0.7,
    //                 'maxOutputTokens' => 800, // Mungkin butuh token lebih banyak untuk analisis mendalam
    //             ]
    //         ]);

    //         if ($response->successful()) {
    //             // Parsing response dari Gemini
    //             $result = $response->json();
    //             return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
    //         } else {
    //             Log::error('Gemini API error: ' . $response->body());
    //             return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Error generating AI insight: ' . $e->getMessage());
    //         return 'Terjadi kesalahan dalam menghasilkan analisis.';
    //     }
    // }
    
    //  private function createAnalysisPrompt($data)
    // {
    //     $periods = implode(", ", $data['periods']);
    //     $values = implode(", ", array_map(fn($v) => 'Rp'.number_format($v,0,',','.'), $data['sales_values']));
        
    //     return "Anda adalah seorang analis bisnis dan data senior di sebuah perusahaan di Indonesia.
        
    //     Berikut adalah data rekap penjualan bulanan dalam Rupiah:
    //     - Periode Data: {$periods}
    //     - Rincian Penjualan per Bulan: {$values}
    //     - Total Penjualan Selama Periode: Rp " . number_format($data['total_sales'], 0, ',', '.') . "
    //     - Rata-rata Penjualan per Bulan: Rp " . number_format($data['average_sales'], 0, ',', '.') . "
    //     - Penjualan Tertinggi dalam Sebulan: Rp " . number_format($data['max_sales'], 0, ',', '.') . "
    //     - Penjualan Terendah dalam Sebulan: Rp " . number_format($data['min_sales'], 0, ',', '.') . "
    //     - Jumlah Data: {$data['data_count']} bulan
        
    //     Tugas Anda adalah membuat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan profesional untuk manajer. Laporan harus mencakup:
    //     1.  **Ringkasan Kinerja:** Jelaskan secara singkat tren penjualan (apakah naik, turun, atau fluktuatif).
    //     2.  **Identifikasi Puncak & Penurunan:** Sebutkan bulan dengan performa terbaik dan terburuk, serta berikan kemungkinan penyebabnya jika ada pola yang terlihat (misalnya, musim liburan, awal tahun, dll.).
    //     3.  **Rekomendasi Strategis:** Berikan 2-3 poin rekomendasi yang konkret dan bisa ditindaklanjuti untuk meningkatkan penjualan di bulan-bulan berikutnya. Contoh: 'Fokuskan promosi pada produk X di bulan Y' atau 'Evaluasi strategi pemasaran di bulan Z'.
    //     4.  **Proyeksi Singkat:** Berikan prediksi kualitatif (bukan angka pasti) untuk bulan berikutnya berdasarkan tren yang ada.

    //     Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.";
    // }

    // private function getRandomRGBA($opacity = 0.7)
    // {
    //     return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    // }

    public function index(Request $request)
{
    $perPage = $request->input('per_page', 12);
    $search = $request->input('search');

    // Query dasar untuk digunakan kembali
    $baseQuery = StatusPaket::query()
        ->when($search, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"]));

    // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
    $allStatusPakets = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

    // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
    $statuspakets = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

    // [FIX] Siapkan data chart dari SEMUA data
    $labels = $allStatusPakets->map(function($item) {
        $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
        // Label yang lebih informatif
        return $item->status . ' (' . $formattedDate . ')';
    })->all();
    
    $data = $allStatusPakets->pluck('total_paket')->all();
    
    $chartData = [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Package Status Report',
            'text' => 'Total Package',
            'data' => $data,
            'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
        ]],
    ];
    
    $aiInsight = null;
    if ($request->has('generate_ai')) {
        // [FIX] Panggil AI dengan SEMUA data, bukan data terpaginasi
        $aiInsight = $this->generateSalesInsight($allStatusPakets, $chartData);
    }
        
    // Anda belum mengirim $aiInsight ke view, mari kita tambahkan
    return view('marketings.statuspaket', compact('statuspakets', 'chartData', 'aiInsight'));
}

    private function generateSalesInsight($reportData, $chartData): string
    {
        // [FIX] Nama variabel diubah agar lebih jelas
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
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data "Status Paket"
            $analysisData = [
                'periods'         => $chartData['labels'],
                'package_values'  => $chartData['datasets'][0]['data'],
                'total_packages'  => $reportData->sum('total_paket'),    // Menggunakan 'total_paket'
                'average_package' => $reportData->avg('total_paket'),     // Menggunakan 'total_paket'
                'max_package'     => $reportData->max('total_paket'),      // Menggunakan 'total_paket'
                'min_package'     => $reportData->min('total_paket'),      // Menggunakan 'total_paket'
                'data_count'      => $reportData->count(),
                // [BARU] Menambahkan data distribusi status untuk analisis yang lebih baik
                'status_distribution' => $reportData->groupBy('status')->map->count()->all(),
            ];

            $prompt = $this->createAnalysisPrompt($analysisData);

            // ... sisa kode pemanggilan API tidak berubah ...
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
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
        // [FIX] Prompt diubah total agar sesuai konteks "Status Paket"
        $periods = implode(', ', $data['periods']);
        $values  = implode(', ', array_map(fn($v) => 'Rp' . number_format($v, 0, ',', '.'), $data['package_values']));
        $total_packages = number_format($data['total_packages'], 0, ',', '.');
        $average_package = number_format($data['average_package'], 0, ',', '.');

        // Mengubah data distribusi status menjadi string yang mudah dibaca
        $statusDistributionStr = '';
        foreach ($data['status_distribution'] as $status => $count) {
            $statusDistributionStr .= "- Status '{$status}': {$count} kali\n";
        }

        return <<<PROMPT
    Anda adalah seorang Manajer Operasional Proyek yang ahli dalam menganalisis data siklus hidup proyek klien.

    Berikut adalah data mengenai nilai total paket berdasarkan statusnya (misalnya: Baru, Proses, Selesai, Pending) per periode waktu.
    - Rincian Laporan (Status - Periode): {$periods}
    - Rincian Nilai Total Paket: {$values}

    **Ringkasan Statistik:**
    - Total Nilai Semua Paket: Rp {$total_packages}
    - Rata-rata Nilai per Paket: Rp {$average_package}
    - Jumlah Laporan: {$data['data_count']}

    **Distribusi Status:**
    {$statusDistributionStr}

    **Tugas Anda:**
    Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk evaluasi internal tim. Laporan harus mencakup:
    1.  **Ringkasan Umum:** Jelaskan secara singkat distribusi status paket. Status mana yang paling dominan? Apakah ini pertanda baik (banyak yang selesai) atau buruk (banyak yang tertunda)?
    2.  **Analisis Nilai per Status:** Fokus pada status dengan nilai total tertinggi dan terendah. Berikan interpretasi. Contoh: "Nilai tertinggi ada pada paket berstatus 'Proses', menandakan banyaknya proyek besar yang sedang berjalan." atau "Nilai pada status 'Baru' rendah, mungkin menandakan perlambatan akuisisi proyek baru bulan ini."
    3.  **Identifikasi Potensi Masalah (Bottleneck):** Berdasarkan distribusi status, identifikasi di mana kemungkinan terjadi penumpukan pekerjaan. Contoh: "Tingginya jumlah paket dengan status 'Pending' menunjukkan adanya hambatan pada tahap awal persetujuan."
    4.  **Rekomendasi Operasional:** Berikan 2-3 poin rekomendasi konkret untuk meningkatkan alur kerja. Contoh: 'Alokasikan lebih banyak sumber daya untuk menyelesaikan paket berstatus 'Proses' yang bernilai tinggi.' atau 'Lakukan follow-up untuk semua paket berstatus 'Pending' lebih dari seminggu.'

    Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.
    PROMPT;
    }
    public function store(Request $request)
    {
        try {
        // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'status' => [
                    'required',
                    Rule::in([
                        'Surat Pesanan',
                        'Surat Pertanggungjawaban',
                        'Keuangan',
                        'Dokumen Akhir',
                        'Finish',
                    ]),
                ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = StatusPaket::where('tanggal', $validatedData['tanggal'])
            ->where('status', $validatedData['status'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            StatusPaket::create($validatedData);
    
            return redirect()->route('statuspaket.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Status Paket: ' . $e->getMessage());
            Log::info('Perusahaan input:', [$request->input('status')]);
            return redirect()->route('statuspaket.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
    public function update(Request $request, StatusPaket $statuspaket)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'status' => [
                'required',
                Rule::in([
                    'Surat Pesanan',
                    'Surat Pertanggungjawaban',
                    'Keuangan',
                    'Dokumen Akhir',
                    'Finish',
                ]),
            ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = StatusPaket::where('tanggal', $validatedData['tanggal'])
            ->where('status', $validatedData['status'])
            ->where('id_statuspaket', '!=', $statuspaket->id_statuspaket)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Tidak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $statuspaket->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('statuspaket.index')->with('success', 'Data berhasil diperbarui.');
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
                ->route('statuspaket.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Package Status Report|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Stats</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Package Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Package Status Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_Status_paket.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(StatusPaket $statuspaket)
    {
        try {
            $statuspaket->delete();
            return redirect()->route('statuspaket.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Status Paket: ' . $e->getMessage());
            return redirect()->route('statuspaket.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanPaketAdministrasiData()
    {
        $data = StatusPaket::all(['tanggal','status','total_paket']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        // Ambil data dari database
        $query = StatusPaket::query();
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
        
        $statuspakets = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();    

        // Format label sesuai kebutuhan
        $labels = $statuspakets->pluck('status')->toArray();
        $data = $statuspakets->pluck('total_paket')->toArray();
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

    public function chartTotal(Request $request)
    {
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    // Ambil data dari database
    $query = StatusPaket::query();
    
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
    
    $statuspakets = $query->get();

    // Akumulasi total penjualan berdasarkan nama website
    $akumulasiData = [];
    foreach ($statuspakets as $item) {
        $namaStatus = $item->status;
        if (!isset($akumulasiData[$namaStatus])) {
            $akumulasiData[$namaStatus] = 0;
        } 
        $akumulasiData[$namaStatus] += $item->total_paket;
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

