<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPendapatanServisAsp;
use App\Traits\DateValidationTrait;
use Exception;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RekapPendapatanServisAspController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $query = RekapPendapatanServisAsp::query();

        // Filter tanggal
        if ($request->filled('start_date')) {
            try {
                // Directly use the date string from the request.
                $startDate = $request->start_date;
                $query->whereDate('tanggal', '>=', $startDate);
            } catch (Exception $e) {
                Log::error("Invalid start_date format provided: " . $request->start_date);
            }
        }

        if ($request->filled('end_date')) {
            try {
                // Directly use the date string from the request.
                $endDate = $request->end_date;
                $query->whereDate('tanggal', '<=', $endDate);
            } catch (Exception $e) {
                Log::error("Invalid end_date format provided: " . $request->end_date);
            }
        }

        // 1) Ambil SEMUA data untuk chart dan AI insight
        $allServiceReports = (clone $query)
            ->orderBy('nilai_pendapatan', 'desc')
            ->get();

        // 2) Ambil data yang dipaginate untuk tampilan tabel
        $rekappendapatanservisasps = (clone $query)
            ->orderBy('nilai_pendapatan', 'desc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));

        // Siapkan data chart
        $labels = $allServiceReports->map(fn($item) => 
            $item->pelaksana . ' (Rp ' . number_format($item->nilai_pendapatan) . ')'
        )->all();
        $data = $allServiceReports->pluck('nilai_pendapatan')->all();

        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER'          => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER'                => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS'        => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA'       => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN'        => 'rgba(153, 102, 255, 0.7)',
        ];
        $backgroundColors = $allServiceReports
            ->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(201, 203, 207, 0.7)')
            ->all();

        $chartData = [
            'labels'   => $labels,
            'datasets' => [[
                'label'           => 'Grafik Rekap Pendapatan Servis ASP',
                'text'            => 'ASP Service Revenue Value',
                'data'            => $data,
                'backgroundColor' => $backgroundColors,
            ]],
        ];

        // Jika minta insight AI
        $aiInsight = null;
        if ($request->has('generate_ai')) {
            $aiInsight = $this->generateServiceRevenueInsight($allServiceReports, $chartData);
        }

        return view('supports.rekappendapatanservisasp', compact(
            'rekappendapatanservisasps',
            'chartData',
            'aiInsight'
        ));
    }

        /**
         * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Pendapatan Servis.
         */
        private function generateServiceRevenueInsight($serviceData, $chartData): string
        {
            $apiKey = config('services.gemini.api_key');
            $apiUrl = config('services.gemini.api_url');

            if (!$apiKey || !$apiUrl) {
                Log::error('Gemini API Key or URL is not configured.');
                return 'Layanan AI tidak terkonfigurasi dengan benar.';
            }

            if ($serviceData->isEmpty()) {
                return 'Tidak ada data pendapatan yang cukup untuk dianalisis.';
            }

            try {
                // [FIX] Menggunakan nama kolom dan variabel yang sesuai
                $analysisData = [
                    'providers'        => $chartData['labels'],
                    'revenue_values'   => $chartData['datasets'][0]['data'],
                    'total_revenue'    => $serviceData->sum('nilai_pendapatan'),    // Menggunakan 'nilai_pendapatan'
                    'average_revenue'  => $serviceData->avg('nilai_pendapatan'),     // Menggunakan 'nilai_pendapatan'
                    'max_revenue'      => $serviceData->max('nilai_pendapatan'),      // Menggunakan 'nilai_pendapatan'
                    'min_revenue'      => $serviceData->min('nilai_pendapatan'),      // Menggunakan 'nilai_pendapatan'
                    'data_count'       => $serviceData->count(),
                    // [BARU] Menambahkan data agregat per pelaksana untuk analisis perbandingan
                    'revenue_per_provider' => $serviceData->groupBy('pelaksana')->map->sum('nilai_pendapatan')->all(),
                ];
                
                // [FIX] Panggil fungsi prompt yang baru
                $prompt = $this->createServiceRevenueAnalysisPrompt($analysisData);

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
         * [FIX] Seluruh prompt dirombak agar sesuai konteks perbandingan performa service provider.
         */
        private function createServiceRevenueAnalysisPrompt(array $data): string
        {
            $total_revenue = number_format($data['total_revenue'], 0, ',', '.');
            
            $providerValuesStr = '';
            foreach ($data['revenue_per_provider'] as $provider => $total) {
                $providerValuesStr .= "- **{$provider}**: Rp " . number_format($total, 0, ',', '.') . "\n";
            }

            return <<<PROMPT
        Anda adalah seorang Manajer Kemitraan Layanan (Service Partnership Manager) yang bertugas mengevaluasi kinerja Authorized Service Provider (ASP).

        Berikut adalah data rekapitulasi total pendapatan servis yang dihasilkan oleh masing-masing pelaksana (ASP) dalam periode waktu tertentu.
        - Total Pendapatan dari Semua Pelaksana: Rp {$total_revenue}
        - Jumlah Total Laporan: {$data['data_count']}

        **Rincian Pendapatan per Pelaksana:**
        {$providerValuesStr}

        **Tugas Anda:**
        Buat laporan analisis singkat dan tajam (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk kepala divisi layanan (Head of Services).

        Analisis harus fokus pada perbandingan performa antar pelaksana.
        1.  **Analisis Kinerja Pelaksana (ASP):** Identifikasi 3 pelaksana dengan kontribusi pendapatan tertinggi (Top Performers) dan pelaksana dengan pendapatan terendah. Jelaskan seberapa signifikan kontribusi para top performers terhadap total pendapatan.
        2.  **Distribusi & Ketergantungan:** Berikan komentar mengenai distribusi pendapatan. Apakah pendapatan terpusat hanya pada satu atau dua pelaksana saja? Jelaskan risiko dari tingkat ketergantungan yang tinggi pada pelaksana tertentu.
        3.  **Rekomendasi Manajemen Mitra (Partner Management):** Berikan 2-3 poin rekomendasi strategis. Contoh: 'Berikan program insentif atau status 'Premium Partner' kepada CV. ARI DISTRIBUTION CENTER untuk menjaga performa dan loyalitas.' atau 'Lakukan sesi business review dengan pelaksana berkinerja rendah untuk mengidentifikasi tantangan dan menyusun rencana perbaikan.'
        4.  **Strategi Pengembangan:** Berdasarkan data, berikan saran untuk pengembangan jaringan ASP ke depan. Contoh: 'Perluas kerjasama dengan lebih banyak mitra untuk mengurangi ketergantungan pada satu provider utama' atau 'Fasilitasi transfer pengetahuan dari mitra berkinerja tinggi ke mitra lainnya.'

        Gunakan format markdown untuk poin-poin agar mudah dibaca.
        PROMPT;
        }
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'CV. ARI DISTRIBUTION CENTER',
                        'CV. BALIYONI COMPUTER',
                        'PT. NABA TECHNOLOGY SOLUTIONS',
                        'CV. ELKA MANDIRI (50%)-SAMITRA',
                        'CV. ELKA MANDIRI (50%)-DETRAN'
                    ]),
                ],
                'nilai_pendapatan' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan pelaksana
            $exists = RekapPendapatanServisAsp::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->exists();

            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPendapatanServisAsp::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            RekapPendapatanServisAsp::create($validatedData);
    
            return redirect()->route('rekappendapatanservisasp.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Rekap Pendapatan Servis ASP Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('rekappendapatanservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPendapatanServisAsp $rekappendapatanservisasp)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                'required',
                Rule::in([
                    'CV. ARI DISTRIBUTION CENTER',
                    'CV. BALIYONI COMPUTER',
                    'PT. NABA TECHNOLOGY SOLUTIONS',
                    'CV. ELKA MANDIRI (50%)-SAMITRA',
                    'CV. ELKA MANDIRI (50%)-DETRAN'
                ]),
            ],

                'nilai_pendapatan' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPendapatanServisAsp::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->where('id_rpsasp', '!=', $rekappendapatanservisasp->id_rpsasp)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
            // Update data
            $rekappendapatanservisasp->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('rekappendapatanservisasp.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()
                ->route('rekappendapatanservisasp.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Support - ASP Service Revenue Recap|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Executor</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Revenue Value (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Pendapatan Servis ASP</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan'/>
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_perusahaan.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }   

    public function destroy(RekapPendapatanServisAsp $rekappendapatanservisasp)
    {
        try {
            $rekappendapatanservisasp->delete();
            return redirect()->route('rekappendapatanservisasp.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Pendapatan Servis ASP Data: ' . $e->getMessage());
            return redirect()->route('rekappendapatanservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function getRekapPenjualaPerusahaannData()
    {
        $data = RekapPendapatanServisAsp::all(['tanggal','pelaksana','nilai_pendapatan']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
{
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');
    
    $query = RekapPendapatanServisAsp::query();
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
    
    $rekappendapatanservisasps = $query
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->get();
    
    $pelaksanaColors = [
        'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
        'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
        'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
        'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
        'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
    ];

    // Gabungkan pelaksana dan nilai_pendapatan untuk label
    $labels = $rekappendapatanservisasps->map(function ($item) {
        return $item->pelaksana . ' ('. 'Rp'. ' ' . number_format($item->nilai_pendapatan) . ')';
    })->toArray();    
    $data = $rekappendapatanservisasps->pluck('nilai_pendapatan')->toArray(); // Nilai pendapatan
    $backgroundColors = $rekappendapatanservisasps->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

    // Format data untuk Pie Chart
    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Grafik Rekap Pendapatan Servis ASP',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ],
        ],
    ];

    // Kembalikan data dalam format JSON
    return response()->json($chartData);
}

public function chartTotal(Request $request)
{
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    // Ambil data dari database dengan filter yang diperlukan
    $query = RekapPendapatanServisAsp::query();

    if ($search) {
        $query->where('tanggal', 'LIKE', "%$search%");
    }

    if ($startMonth && $endMonth) {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Ambil data yang sudah difilter
    $rekappendapatanservisasps = $query->get();

    // Akumulasi total piutang berdasarkan pelaksana
    $akumulasiData = $rekappendapatanservisasps->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_pendapatan'));

    // Warna berdasarkan nama pelaksana
    $pelaksanaColors = [
        'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
        'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
        'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
        'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
        'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
    ];

    // Siapkan data untuk chart
    $labels = $akumulasiData->keys()->toArray();
    $data = $akumulasiData->values()->toArray();
    $backgroundColors = array_map(fn($label) => $pelaksanaColors[$label] ?? 'rgba(0, 0, 0, 0.7)', $labels);

    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Total Piutang',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ],
        ],
    ];

    return response()->json($chartData);
}

}

