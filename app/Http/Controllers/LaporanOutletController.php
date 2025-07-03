<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanOutlet;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LaporanOutletController extends Controller
{
    use DateValidationTrait;

    //     public function index(Request $request)
    //     { 
    //         $perPage = $request->input('per_page', 12);
    //         $search = $request->input('search');

    //         #$query = KasHutangPiutang::query();

    //         // Query untuk mencari berdasarkan tahun dan date
    //         $laporanoutlets = LaporanOutlet::query()
    //             ->when($search, function ($query, $search) {
    //                 return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
    //             })
    //             ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //             ->paginate($perPage);

    //         // Hitung total untuk masing-masing kategori
    //         $totalPenjualan = $laporanoutlets->sum('total_pembelian');

    //         // Siapkan data untuk chart
    //         function getRandomRGBA($opacity = 0.7) {
    //             return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //         }

    //         $labels = $laporanoutlets->pluck('tanggal')->map(function ($date) {
    //             return \Carbon\Carbon::parse($date)->translatedFormat('F - Y');
    //         })->toArray();        
    //         $data = $laporanoutlets->pluck('total_pembelian')->toArray();

    //         // Generate random colors for each data item
    //         $backgroundColors = array_map(fn() => getRandomRGBA(), $data);

    //         $chartData = [
    //             'labels' => $labels, // Labels untuk chart
    //             'datasets' => [
    //                 [
    //                     'label' => 'Grafik Laporan Pembelian Outlet', // Nama dataset
    //                     'text' => 'Total Purchasing', // Nama dataset
    //                     'data' => $data, // Data untuk chart
    //                     'backgroundColor' => $backgroundColors, // Warna batang random
    //                 ],
    //             ],
    //         ];
    //         $aiInsight = null;

    //     // 2. Hanya jalankan fungsi AI jika request memiliki parameter 'generate_ai'.
    //     if ($request->has('generate_ai')) {
    //         $aiInsight = $this->generateSalesInsight($laporanoutlets, $chartData);
    //     }

    //         return view('procurements.laporanoutlet', compact('laporanoutlets', 'chartData','aiInsight'));    }
    //  private function generateSalesInsight($salesData, $chartData)
    //     {
    //         // Ambil konfigurasi dari file config/services.php
    //         $apiKey = config('services.gemini.api_key');
    //         $apiUrl = config('services.gemini.api_url');

    //         if (!$apiKey || !$apiUrl) {
    //             Log::error('Gemini API Key or URL is not configured.');
    //             return 'Layanan AI tidak terkonfigurasi dengan benar.';
    //         }

    //         // Jangan panggil AI jika tidak ada data untuk dianalisis
    //         if ($salesData->isEmpty()) {
    //             return 'Tidak ada data penjualan yang cukup untuk dianalisis.';
    //         }

    //         try {
    //             $analysisData = [
    //                 'periods' => $chartData['labels'],
    //                 'sales_values' => $chartData['datasets'][0]['data'],
    //                 'total_sales' => $salesData->sum('total_penjualan'),
    //                 'average_sales' => $salesData->avg('total_penjualan'),
    //                 'max_sales' => $salesData->max('total_penjualan'),
    //                 'min_sales' => $salesData->min('total_penjualan'),
    //                 'data_count' => $salesData->count(),
    //             ];

    //             $prompt = $this->createAnalysisPrompt($analysisData);

    //             // Kirim request ke API Gemini dengan format yang BENAR
    //             $response = Http::withHeaders([
    //                 'Content-Type' => 'application/json',
    //             ])->post($apiUrl . '?key=' . $apiKey, [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             ['text' => $prompt]
    //                         ]
    //                     ]
    //                 ],
    //                 'generationConfig' => [
    //                     'temperature' => 0.7,
    //                     'maxOutputTokens' => 800, // Mungkin butuh token lebih banyak untuk analisis mendalam
    //                 ]
    //             ]);

    //             if ($response->successful()) {
    //                 // Parsing response dari Gemini
    //                 $result = $response->json();
    //                 return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari AI.';
    //             } else {
    //                 Log::error('Gemini API error: ' . $response->body());
    //                 return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Error generating AI insight: ' . $e->getMessage());
    //             return 'Terjadi kesalahan dalam menghasilkan analisis.';
    //         }
    //     }

    //      private function createAnalysisPrompt($data)
    //     {
    //         $periods = implode(", ", $data['periods']);
    //         $values = implode(", ", array_map(fn($v) => 'Rp'.number_format($v,0,',','.'), $data['sales_values']));

    //         return "Anda adalah seorang analis bisnis dan data senior di sebuah perusahaan di Indonesia.

    //         Berikut adalah data rekap penjualan bulanan dalam Rupiah:
    //         - Periode Data: {$periods}
    //         - Rincian Penjualan per Bulan: {$values}
    //         - Total Penjualan Selama Periode: Rp " . number_format($data['total_sales'], 0, ',', '.') . "
    //         - Rata-rata Penjualan per Bulan: Rp " . number_format($data['average_sales'], 0, ',', '.') . "
    //         - Penjualan Tertinggi dalam Sebulan: Rp " . number_format($data['max_sales'], 0, ',', '.') . "
    //         - Penjualan Terendah dalam Sebulan: Rp " . number_format($data['min_sales'], 0, ',', '.') . "
    //         - Jumlah Data: {$data['data_count']} bulan

    //         Tugas Anda adalah membuat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan profesional untuk manajer. Laporan harus mencakup:
    //         1.  **Ringkasan Kinerja:** Jelaskan secara singkat tren penjualan (apakah naik, turun, atau fluktuatif).
    //         2.  **Identifikasi Puncak & Penurunan:** Sebutkan bulan dengan performa terbaik dan terburuk, serta berikan kemungkinan penyebabnya jika ada pola yang terlihat (misalnya, musim liburan, awal tahun, dll.).
    //         3.  **Rekomendasi Strategis:** Berikan 2-3 poin rekomendasi yang konkret dan bisa ditindaklanjuti untuk meningkatkan penjualan di bulan-bulan berikutnya. Contoh: 'Fokuskan promosi pada produk X di bulan Y' atau 'Evaluasi strategi pemasaran di bulan Z'.
    //         4.  **Proyeksi Singkat:** Berikan prediksi kualitatif (bukan angka pasti) untuk bulan berikutnya berdasarkan tren yang ada.

    //         Gunakan format markdown untuk penomoran atau poin-poin agar mudah dibaca.";
    //     }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanOutlet::query()
            ->when($search, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"]));

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allOutletReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporanoutlets = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allOutletReports->pluck('tanggal')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->translatedFormat('F Y'))->all();

        // [FIX] Gunakan kolom 'total_pembelian'
        $data = $allOutletReports->pluck('total_pembelian')->all();

        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Laporan Pembelian Outlet',
                'text' => 'Total Pembelian',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generatePurchasingInsight($allOutletReports, $chartData);
        }

        return view('procurements.laporanoutlet', compact('laporanoutlets', 'chartData', 'aiInsight'));
    }
    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Pembelian.
     */
    private function generatePurchasingInsight($purchasingData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($purchasingData->isEmpty()) {
            return 'Tidak ada data pembelian yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data "Laporan Pembelian Outlet"
            $analysisData = [
                'periods'           => $chartData['labels'],
                'purchasing_values' => $chartData['datasets'][0]['data'],
                'total_purchasing'  => $purchasingData->sum('total_pembelian'),    // Menggunakan 'total_pembelian'
                'average_purchasing' => $purchasingData->avg('total_pembelian'),     // Menggunakan 'total_pembelian'
                'max_purchasing'    => $purchasingData->max('total_pembelian'),      // Menggunakan 'total_pembelian'
                'min_purchasing'    => $purchasingData->min('total_pembelian'),      // Menggunakan 'total_pembelian'
                'data_count'        => $purchasingData->count(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createPurchasingAnalysisPrompt($analysisData);

            // ... sisa kode pemanggilan API tidak berubah ...
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 800],
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
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Pembelian (Procurement).
     */
    private function createPurchasingAnalysisPrompt(array $data): string
    {
        $periods = implode(', ', $data['periods']);
        $values  = implode(', ', array_map(fn($v) => 'Rp' . number_format($v, 0, ',', '.'), $data['purchasing_values']));
        $total_purchasing = number_format($data['total_purchasing'], 0, ',', '.');
        $average_purchasing = number_format($data['average_purchasing'], 0, ',', '.');
        $max_purchasing = number_format($data['max_purchasing'], 0, ',', '.');
        $min_purchasing = number_format($data['min_purchasing'], 0, ',', '.');

        return <<<PROMPT
        Anda adalah seorang Manajer Pengadaan (Procurement Manager) yang sangat teliti dan ahli dalam efisiensi biaya.

        Berikut adalah data rekapitulasi total nilai pembelian (purchasing) untuk outlet per periode waktu.
        - Periode Data: {$periods}
        - Rincian Nilai Pembelian per Periode: {$values}

        **Ringkasan Statistik Pembelian:**
        - Total Nilai Pembelian: Rp {$total_purchasing}
        - Rata-rata Nilai Pembelian per Periode: Rp {$average_purchasing}
        - Pembelian Tertinggi dalam Satu Periode: Rp {$max_purchasing}
        - Pembelian Terendah dalam Satu Periode: Rp {$min_purchasing}
        - Jumlah Periode Data: {$data['data_count']}

        **Tugas Anda:**
        Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk kepala departemen pengadaan atau direktur keuangan.

        Analisis harus mencakup:
        1.  **Analisis Tren Pengeluaran:** Jelaskan pola pengeluaran untuk pembelian. Apakah ada tren kenaikan atau penurunan biaya? Apakah pengeluaran bersifat musiman atau stabil?
        2.  **Identifikasi Puncak & Penurunan Belanja:** Sebutkan periode dengan pengeluaran tertinggi dan terendah. Berikan hipotesis mengapa ini terjadi (misal: "Pembelian memuncak pada bulan Juli, kemungkinan besar untuk persiapan stok menjelang musim ramai," atau "Pengeluaran sangat rendah pada Februari, perlu dipastikan apakah ini karena negosiasi harga yang baik atau karena ada penundaan pengadaan.").
        3.  **Rekomendasi Efisiensi Biaya:** Berdasarkan data, berikan 2-3 poin rekomendasi konkret untuk mengoptimalkan biaya pembelian. Contoh: 'Identifikasi pemasok utama pada periode belanja puncak dan negosiasikan kontrak jangka panjang untuk mendapatkan harga lebih baik.' atau 'Evaluasi penyebab belanja rendah di bulan X, apakah strategi tersebut bisa direplikasi untuk efisiensi.'
        4.  **Perencanaan Anggaran (Budgeting):** Berikan saran singkat mengenai penyusunan anggaran pembelian untuk periode selanjutnya berdasarkan tren historis ini.

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
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_pembelian' => 'required|integer|min:0'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanOutlet::where('tanggal', $validatedData['tanggal'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanOutlet::create($validatedData);

            return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Outlet data: ' . $e->getMessage());
            return redirect()->route('laporanoutlet.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanOutlet $laporanoutlet)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_pembelian' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = LaporanOutlet::where('tanggal', $validatedData['tanggal'])
                ->where('id_outlet', '!=', $laporanoutlet->id_outlet)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            // Update data
            $laporanoutlet->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanoutlet.index')
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
                ->route('laporanoutlet.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Outlet Purchase Report|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Purchase Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Outlet Purchase Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);


            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_pembelian_outlet.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanOutlet $laporanoutlet)
    {
        try {
            $laporanoutlet->delete();
            return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Stok: ' . $e->getMessage());
            return redirect()->route('laporanoutlet.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanStokData()
    {
        $data = LaporanOutlet::all(['tanggal', 'total_pembelian']);

        return response()->json($data);
    }

    public function showChart(Request $request)
    {

        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanOutlet::query();
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

        $laporanoutlets = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Format label sesuai kebutuhan
        $labels = $laporanoutlets->pluck('tanggal')->map(function ($tanggal) {
            return \Carbon\Carbon::parse($tanggal)->translatedFormat('F - Y');
        })->toArray();
        $data = $laporanoutlets->pluck('total_pembelian')->toArray();
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
