<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPenjualan;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Traits\DateValidationTrait;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class RekapPenjualanController extends Controller
{
    use DateValidationTrait;

    //  public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');
        
    //     $rekappenjualans = RekapPenjualan::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
    //         ->paginate($perPage);

    //     // Bagian ini sudah benar
    //     $rekappenjualans->map(function ($item) {
    //         $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
    //         return $item;
    //     });

    //     $labels = $rekappenjualans->map(function ($item) {
    //         return Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //     })->toArray();
        
    //     $data = $rekappenjualans->pluck('total_penjualan')->toArray();
    //     $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

    //     $chartData = [
    //         'labels' => $labels,
    //         'datasets' => [
    //             [
    //                 'text' => 'Total Sales',
    //                 'data' => $data,
    //                 'backgroundColor' => $backgroundColors,
    //             ],
    //         ],
    //     ];
        
    //     // Panggil fungsi generateSalesInsight yang SUDAH DIPERBAIKI
    //     $aiInsight = $this->generateSalesInsight($rekappenjualans, $chartData);

    //     return view('marketings.rekappenjualan', compact('rekappenjualans', 'chartData','aiInsight'));
    // }

     public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        
        $rekappenjualans = RekapPenjualan::query()
            ->when($search, function ($query, $search) {
                return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);

        // Bagian ini sudah benar
        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });

        $labels = $rekappenjualans->map(function ($item) {
            return Carbon::parse($item->tanggal)->translatedFormat('F Y');
        })->toArray();
        
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBA(), $data);

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'text' => 'Total Sales',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
        $aiInsight = null;

    // 2. Hanya jalankan fungsi AI jika request memiliki parameter 'generate_ai'.
    if ($request->has('generate_ai')) {
        $aiInsight = $this->generateSalesInsight($rekappenjualans, $chartData);
    }
        return view('marketings.rekappenjualan', compact('rekappenjualans', 'chartData','aiInsight'));
    }
      private function generateSalesInsight($salesData, $chartData)
    {
        // Ambil konfigurasi dari file config/services.php
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }
        
        // Jangan panggil AI jika tidak ada data untuk dianalisis
        if ($salesData->isEmpty()) {
            return 'Tidak ada data penjualan yang cukup untuk dianalisis.';
        }

        try {
            $analysisData = [
                'periods' => $chartData['labels'],
                'sales_values' => $chartData['datasets'][0]['data'],
                'total_sales' => $salesData->sum('total_penjualan'),
                'average_sales' => $salesData->avg('total_penjualan'),
                'max_sales' => $salesData->max('total_penjualan'),
                'min_sales' => $salesData->min('total_penjualan'),
                'data_count' => $salesData->count(),
            ];
            
            $prompt = $this->createAnalysisPrompt($analysisData);
            
            // Kirim request ke API Gemini dengan format yang BENAR
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800, // Mungkin butuh token lebih banyak untuk analisis mendalam
                ]
            ]);

            if ($response->successful()) {
                // Parsing response dari Gemini
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
            } else {
                Log::error('Gemini API error: ' . $response->body());
                return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
            }
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }
    
     private function createAnalysisPrompt($data)
    {
        $periods = implode(", ", $data['periods']);
        $values = implode(", ", array_map(fn($v) => 'Rp'.number_format($v,0,',','.'), $data['sales_values']));
        
        return "Anda adalah seorang analis bisnis dan data senior di sebuah perusahaan di Indonesia.
        
        Berikut adalah data rekap penjualan bulanan dalam Rupiah:
        - Periode Data: {$periods}
        - Rincian Penjualan per Bulan: {$values}
        - Total Penjualan Selama Periode: Rp " . number_format($data['total_sales'], 0, ',', '.') . "
        - Rata-rata Penjualan per Bulan: Rp " . number_format($data['average_sales'], 0, ',', '.') . "
        - Penjualan Tertinggi dalam Sebulan: Rp " . number_format($data['max_sales'], 0, ',', '.') . "
        - Penjualan Terendah dalam Sebulan: Rp " . number_format($data['min_sales'], 0, ',', '.') . "
        - Jumlah Data: {$data['data_count']} bulan
        
        Tugas Anda adalah membuat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan profesional untuk manajer. Laporan harus mencakup:
        1.  **Ringkasan Kinerja:** Jelaskan secara singkat tren penjualan (apakah naik, turun, atau fluktuatif).
        2.  **Identifikasi Puncak & Penurunan:** Sebutkan bulan dengan performa terbaik dan terburuk, serta berikan kemungkinan penyebabnya jika ada pola yang terlihat (misalnya, musim liburan, awal tahun, dll.).
        3.  **Rekomendasi Strategis:** Berikan 2-3 poin rekomendasi yang konkret dan bisa ditindaklanjuti untuk meningkatkan penjualan di bulan-bulan berikutnya. Contoh: 'Fokuskan promosi pada produk X di bulan Y' atau 'Evaluasi strategi pemasaran di bulan Z'.
        4.  **Proyeksi Singkat:** Berikan prediksi kualitatif (bukan angka pasti) untuk bulan berikutnya berdasarkan tren yang ada.

        Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.";
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
                'total_penjualan' => 'required|integer|min:0',
            ]);

            // Validasi tanggal menggunakan trait
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek apakah data sudah ada (setelah validasi tanggal)
            if (RekapPenjualan::where('tanggal', $validatedData['tanggal'])->exists()) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Simpan ke database
            RekapPenjualan::create($validatedData);
            return redirect()->route('rekappenjualan.index')->with('success', 'Data Berhasil Ditambahkan');

        } catch (Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPenjualan $rekappenjualan)
    {
        try {
            // Konversi tanggal agar selalu dalam format Y-m-d
            if ($request->has('tanggal')) {
                try {
                    $request->merge(['tanggal' => \Carbon\Carbon::parse($request->input('tanggal'))->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }
    
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_penjualan' => 'required|integer|min:0',
            ]);
    
            // Validasi tanggal menggunakan trait
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            // Bersihkan total_penjualan agar hanya angka
            $validatedData['total_penjualan'] = preg_replace('/\D/', '', $validatedData['total_penjualan']);
    
            // Cek apakah tanggal sudah ada di database untuk perusahaan lain
            $exists = RekapPenjualan::where('tanggal', $validatedData['tanggal'])
                ->where('id_rp', '!=', $rekappenjualan->id_rp)
                ->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
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
            $mpdf->SetFooter('{DATE j-m-Y}Marketing Report - Sales Recap|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Sales (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Chart Sales Report</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // 
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_rekap_penjualan.pdf\"');
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

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = RekapPenjualan::query();
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
        
        $rekappenjualans = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });

        // Format labels as "Month - Year"
        $labels = $rekappenjualans->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
        })->toArray();

        // Numeric data for the chart
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();

        // Generate random background colors for each data point
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

    return response()->json([
        'labels' => $labels, // Properly formatted labels
        'datasets' => [
            [
                'label' => 'Total Penjualan', // Label for the dataset
                'data' => $data, // Numeric data for chart rendering
                'backgroundColor' => $backgroundColors, // Random colors
            ],
        ],
    ]);
}

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
    

    public function exportView(Request $request)
    {
    $perPage = $request->input('per_page', 100); // Bisa set jumlah besar untuk ekspor
    $search = $request->input('search');

    $rekappenjualans = RekapPenjualan::query()
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->paginate($perPage)
        ->withQueryString();

    // Format tambahan untuk tampilan
    $rekappenjualans->map(function ($item) {
        $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
        return $item;
    });

    // Data chart
    function getRandomRGBA2($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    $labels = $rekappenjualans->map(function ($item) {
        return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    })->toArray();

    $data = $rekappenjualans->pluck('total_penjualan')->toArray();
    $backgroundColors = array_map(fn() => getRandomRGBA2(), $data);

    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'text' => 'Total Penjualan',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ],
        ],
    ];

    // Ganti return view-nya dengan exports.rekap-penjualan
    return view('exports.rekap-penjualan', compact('rekappenjualans', 'chartData'));
}

}
