<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasHutangPiutang;
use App\Traits\DateValidationTraitAccSPI;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class KHPSController extends Controller
{
    use DateValidationTraitAccSPI;

    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $kashutangpiutangstoks = KasHutangPiutang::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('tanggal', 'LIKE', "%$search%");
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalKas = $kashutangpiutangstoks->sum('kas');
    //     $totalHutang = $kashutangpiutangstoks->sum('hutang');
    //     $totalPiutang = $kashutangpiutangstoks->sum('piutang');
    //     $totalStok = $kashutangpiutangstoks->sum('stok');

    //     // Format angka menjadi format rupiah atau format angka biasa
    //     $formattedKas = number_format($totalKas, 0, ',', '.');
    //     $formattedHutang = number_format($totalHutang, 0, ',', '.');
    //     $formattedPiutang = number_format($totalPiutang, 0, ',', '.');
    //     $formattedStok = number_format($totalStok, 0, ',', '.');

    //     $chartData = [
    //         'labels' => [
    //             "Kas : Rp $formattedKas",
    //             "Hutang : Rp $formattedHutang",
    //             "Piutang : Rp $formattedPiutang",
    //             "Stok : Rp $formattedStok",
    //         ],
    //         'datasets' => [
    //             [
    //                 'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
    //                 'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'], // Warna untuk pie chart
    //                 'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
    //             ],
    //         ],
    //     ];
    //     return view('accounting.khps', compact('kashutangpiutangstoks', 'chartData'));
    // }
    // Kontroller asli 

    //Controller untuk AI
//     public function index(Request $request)
//     {
//         $perPage = $request->input('per_page', 12);
//         $search = $request->input('search');

//         $kashutangpiutangstoks = KasHutangPiutang::query()
//             ->when($search, function ($query, $search) {
//                 return $query->where('tanggal', 'LIKE', "%$search%");
//             })
//             ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
//             ->paginate($perPage);

//         $totalKas = $kashutangpiutangstoks->sum('kas');
//         $totalHutang = $kashutangpiutangstoks->sum('hutang');
//         $totalPiutang = $kashutangpiutangstoks->sum('piutang');
//         $totalStok = $kashutangpiutangstoks->sum('stok');

//         $formattedKas = number_format($totalKas, 0, ',', '.');
//         $formattedHutang = number_format($totalHutang, 0, ',', '.');
//         $formattedPiutang = number_format($totalPiutang, 0, ',', '.');
//         $formattedStok = number_format($totalStok, 0, ',', '.');

//         $chartData = [
//             'labels' => [
//                 "Kas : Rp $formattedKas",
//                 "Hutang : Rp $formattedHutang",
//                 "Piutang : Rp $formattedPiutang",
//                 "Stok : Rp $formattedStok",
//             ],
//             'datasets' => [
//                 [
//                     'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
//                     'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'],
//                     'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
//                 ],
//             ],
//         ];

//         $aiInsight = $this->generateSalesInsight($kashutangpiutangstoks, $chartData);

//         return view('accounting.khps', compact('kashutangpiutangstoks', 'chartData', 'aiInsight'));
//     }

//     private function generateSalesInsight($kashutangpiutangstoks, $chartData): string
// {
//     $apiKey = config('services.gemini.api_key');
//     $apiUrl = config('services.gemini.api_url');

//     if (!$apiKey || !$apiUrl) {
//         Log::error('Gemini API Key or URL is not configured.');
//         return 'Layanan AI tidak terkonfigurasi dengan benar.';
//     }

//     // Check if the core data points are all zero, indicating no meaningful data to analyze
//     if (
//         empty($data['kas']) &&
//         empty($data['hutang']) &&
//         empty($data['piutang']) &&
//         empty($data['stok'])
//     ) {
//         return 'Tidak ada data keuangan atau stok yang cukup untuk dianalisis.';
//     }

//     $prompt = "Berikut adalah data keuangan dan stok: \n\n";
//     $prompt .= "- **Total Kas:** " . number_format($data['kas'], 0, ',', '.') . " IDR\n";
//     $prompt .= "- **Total Hutang:** " . number_format($data['hutang'], 0, ',', '.') . " IDR\n";
//     $prompt .= "- **Total Piutang:** " . number_format($data['piutang'], 0, ',', '.') . " IDR\n";
//     $prompt .= "- **Total Stok:** " . number_format($data['stok'], 0, ',', '.') . " unit/nilai\n\n";
//     $prompt .= "Periode analisis: " . $data['periode_analisis'] . ".\n\n";
//     $prompt .= "Mohon berikan analisis singkat dan relevan tentang kondisi keuangan dan stok berdasarkan data di atas. Identifikasi potensi masalah atau kekuatan, serta berikan saran singkat jika memungkinkan. Fokus pada poin-poin penting yang dapat membantu pengambilan keputusan. Jelaskan juga bagaimana perbandingan antar metrik ini bisa diinterpretasikan.";

//     return $prompt;
// }

//     public function store(Request $request)
//     {
//         try {
//             $validatedData = $request->validate([
//                 'tanggal' => 'required|date',
//                 'kas' => 'required|integer|min:0',
//                 'hutang' => 'required|integer|min:0',
//                 'piutang' => 'required|integer|min:0',
//                 'stok' => 'required|integer|min:0'
//             ]);
//             $errorMessage = '';
//             if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
//                 return redirect()->back()->with('error', $errorMessage);
//             }

//             // Cek kombinasi unik date dan perusahaan
//             $exists = KasHutangPiutang::where('tanggal', $validatedData['tanggal'])->exists();
    
//             if ($exists) {
//                 return redirect()->back()->with('error', 'Data sudah ada.');
//             }
    
//             KasHutangPiutang::create($validatedData);
    
//             return redirect()->route('khps.index')->with('success', 'Data Berhasil Ditambahkan');
//         } catch (\Exception $e) {
//             Log::error('Error storing KHPS data: ' . $e->getMessage());
//             return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
//         }
//     }

 public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $kashutangpiutangstoks = KasHutangPiutang::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);

        $totalKas = $kashutangpiutangstoks->sum('kas');
        $totalHutang = $kashutangpiutangstoks->sum('hutang');
        $totalPiutang = $kashutangpiutangstoks->sum('piutang');
        $totalStok = $kashutangpiutangstoks->sum('stok');

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
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'],
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                ],
            ],
        ];

        // --- Corrected AI Insight Generation ---
        $dataForAI = [
            'kas' => $totalKas,
            'hutang' => $totalHutang,
            'piutang' => $totalPiutang,
            'stok' => $totalStok,
            'periode_analisis' => $search ? "untuk tanggal yang mengandung '$search'" : "dari semua data yang ditampilkan",
        ];

        // Call the AI analysis function with the correctly formatted data
        $aiInsight = $this->generateFinancialAndStockInsight($dataForAI);
        // --- End Corrected AI Insight Generation ---

        return view('accounting.khps', compact('kashutangpiutangstoks', 'chartData', 'aiInsight'));
    }

    /**
     * Generates an AI-powered financial and stock insight using Gemini.
     *
     * @param array $data Contains 'kas', 'hutang', 'piutang', 'stok', and 'periode_analisis'.
     * @return string The AI-generated analysis or an error message.
     */
private function generateFinancialAndStockInsight(array $data): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if (
            ($data['kas'] ?? 0) === 0 &&
            ($data['hutang'] ?? 0) === 0 &&
            ($data['piutang'] ?? 0) === 0 &&
            ($data['stok'] ?? 0) === 0
        ) {
            return 'Tidak ada data keuangan atau stok yang cukup untuk dianalisis.';
        }

        $prompt = "Anda adalah seorang analis keuangan dan operasional senior yang membuat laporan dalam format teks biasa yang bersih dan formal untuk dikirim melalui email kepada manajemen.\n\n";
        $prompt .= "**Aturan Penulisan (SANGAT PENTING):**\n";
        $prompt .= "1.  **JANGAN GUNAKAN FORMAT MARKDOWN.** Jangan gunakan simbol hashtag (`#`), bintang (`*`), atau strip (`-`).\n";
        $prompt .= "2.  Gunakan judul bagian dalam format teks biasa, ditulis dengan huruf kapital di setiap awal kata, dan diakhiri dengan titik dua (contoh: `Kondisi Keuangan:`).\n";
        $prompt .= "3.  Pisahkan setiap bagian utama dengan **satu baris kosong** untuk memastikan keterbacaan.\n";
        $prompt .= "4.  Tulis analisis dalam bentuk paragraf singkat yang jelas.\n\n";

        $prompt .= "Berikut adalah data keuangan dan stok untuk dianalisis:\n\n";
        $prompt .= "Total Kas: Rp " . number_format($data['kas'], 0, ',', '.') . "\n";
        $prompt .= "Total Hutang: Rp " . number_format($data['hutang'], 0, ',', '.') . "\n";
        $prompt .= "Total Piutang: Rp " . number_format($data['piutang'], 0, ',', '.') . "\n";
        $prompt .= "Total Nilai Stok: Rp " . number_format($data['stok'], 0, ',', '.') . "\n";
        $prompt .= "Periode Analisis: " . $data['periode_analisis'] . "\n\n";

        $prompt .= "Mohon buatkan analisis singkat dan relevan berdasarkan data di atas, dengan mengikuti format penulisan yang telah ditetapkan.\n\n";

        $prompt .= "--- CONTOH STRUKTUR WAJIB DIIKUTI ---\n\n";
        $prompt .= "Kondisi Keuangan:\n";
        $prompt .= "[Analisis terpadu mengenai likuiditas (perbandingan kas dan hutang) dan kondisi piutang dalam satu paragraf.]\n\n";

        $prompt .= "Kondisi Stok:\n";
        $prompt .= "[Analisis mengenai nilai stok sebagai aset dan potensi risikonya dalam satu paragraf.]\n\n";

        $prompt .= "Implikasi dan Perbandingan Metrik:\n";
        $prompt .= "[Jelaskan gambaran besar dari kombinasi semua metrik. Apa arti dari angka-angka ini jika dilihat bersamaan?]\n\n";

        $prompt .= "Saran Strategis:\n";
        $prompt .= "[Berikan 2-3 saran konkret dalam bentuk kalimat atau paragraf singkat. Awali setiap saran di baris baru tanpa simbol apapun.]\n\n";

        $prompt .= "Kesimpulan:\n";
        $prompt .= "[Berikan kesimpulan ringkas dalam satu atau dua kalimat.]";        // --- END MODIFIED PROMPT ---

        try {
            $client = new Client();
            $response = $client->post("{$apiUrl}?key={$apiKey}", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            ]);

            $geminiResponse = json_decode($response->getBody()->getContents(), true);

            if (isset($geminiResponse['candidates'][0]['content']['parts'][0]['text'])) {
                return $geminiResponse['candidates'][0]['content']['parts'][0]['text'];
            } else {
                Log::error('Gemini response missing expected text content: ' . json_encode($geminiResponse));
                return 'Gagal mendapatkan analisis dari AI (respons tidak lengkap).';
            }

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
            return 'Terjadi kesalahan saat menghubungi layanan AI: ' . $e->getMessage();
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Cash, Debts, Receivables, and Stock Reports|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='gap: 100px; width: 100%;'>
                <div style='width: 45%; float: left; padding-right: 20px;'>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Table Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Cash (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Debts (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Receivables (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Stock (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
                        </div>
                <div style='width: 45%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Cash, Debts, Receivables, and Stock Charts</h2>
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

