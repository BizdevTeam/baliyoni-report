<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class ArusKasController extends Controller
{
    use DateValidationTraitAccSPI;

    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $aruskass = ArusKas::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('tanggal', 'LIKE', "%$search%");
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $kasMasuk = $aruskass->sum('kas_masuk');   
    //     $kasKeluar = $aruskass->sum('kas_keluar');

    //     // Format angka menjadi format rupiah atau format angka biasa
    //     $formattedKasMasuk = number_format($kasMasuk, 0, ',', '.');
    //     $formattedKasKeluar = number_format($kasKeluar, 0, ',', '.');

    //     // Siapkan data untuk chart dengan menampilkan nilai
    //     $chartData = [
    //         'labels' => [
    //             "Kas Masuk: Rp $formattedKasMasuk",
    //             "Kas Keluar: Rp $formattedKasKeluar"
    //         ],
    //         'datasets' => [
    //             [
    //                 'data' => [$kasMasuk, $kasKeluar],
    //                 'backgroundColor' => ['#1c64f2', '#ff2323'], // Warna untuk pie chart
    //                 'hoverBackgroundColor' => ['#2b6cb0', '#dc2626'],
    //             ],
    //         ],
    //     ];

    //     return view('accounting.aruskas', compact('aruskass', 'chartData'));
    // }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = ArusKas::query()
            ->when($search, fn($q) => $q->where('tanggal', 'LIKE', "%{$search}%"));

        // [FIX] Ambil SEMUA data untuk analisis dan kalkulasi total yang akurat
        $allCashFlows = (clone $baseQuery)->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $aruskass = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Hitung total dari SEMUA data, bukan dari data terpaginasi
        $kasMasuk = $allCashFlows->sum('kas_masuk');
        $kasKeluar = $allCashFlows->sum('kas_keluar');

        $formattedKasMasuk = number_format($kasMasuk, 0, ',', '.');
        $formattedKasKeluar = number_format($kasKeluar, 0, ',', '.');

        // Siapkan data chart (logika ini sudah benar)
        $chartData = [
            'labels' => [
                "Kas Masuk: Rp {$formattedKasMasuk}",
                "Kas Keluar: Rp {$formattedKasKeluar}"
            ],
            'datasets' => [[
                'data' => [$kasMasuk, $kasKeluar],
                'backgroundColor' => ['#1c64f2', '#ff2323'],
                'hoverBackgroundColor' => ['#2b6cb0', '#dc2626'],
            ]],
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateCashFlowInsight($allCashFlows);
        }

        return view('accounting.aruskas', compact('aruskass', 'chartData', 'aiInsight'));
    }

    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Arus Kas.
     */
    private function generateCashFlowInsight($cashFlowData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($cashFlowData->isEmpty()) {
            return 'Tidak ada data arus kas yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Siapkan data analisis yang relevan untuk Arus Kas
            $totalIn = $cashFlowData->sum('kas_masuk');
            $totalOut = $cashFlowData->sum('kas_keluar');
            $netCashFlow = $totalIn - $totalOut;
            // Hindari pembagian dengan nol
            $cashFlowRatio = $totalOut > 0 ? $totalIn / $totalOut : 0;

            $analysisData = [
                'total_cash_in'  => $totalIn,
                'total_cash_out' => $totalOut,
                'net_cash_flow'  => $netCashFlow,
                'cash_flow_ratio' => $cashFlowRatio,
                'data_count'     => $cashFlowData->count(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createCashFlowAnalysisPrompt($analysisData);

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
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Analisis Arus Kas.
     */
    private function createCashFlowAnalysisPrompt(array $data): string
    {
        $total_in_formatted = 'Rp ' . number_format($data['total_cash_in'], 0, ',', '.');
        $total_out_formatted = 'Rp ' . number_format($data['total_cash_out'], 0, ',', '.');
        $net_flow_formatted = 'Rp ' . number_format($data['net_cash_flow'], 0, ',', '.');
        $net_flow_status = $data['net_cash_flow'] >= 0 ? 'Positif (Surplus)' : 'Negatif (Defisit)';
        $ratio_formatted = number_format($data['cash_flow_ratio'], 2, ',', '.');

        return <<<PROMPT
        Anda adalah seorang Manajer Keuangan (Finance Manager) atau Akuntan Senior yang ahli dalam menganalisis kesehatan keuangan perusahaan berdasarkan arus kas.

        Berikut adalah data rekapitulasi Arus Kas untuk periode yang dipilih:
        - Total Kas Masuk (Inflow): {$total_in_formatted}
        - Total Kas Keluar (Outflow): {$total_out_formatted}
        - **Arus Kas Bersih (Net Cash Flow): {$net_flow_formatted}**
        - **Status Arus Kas: {$net_flow_status}**
        - Rasio Arus Kas (Kas Masuk / Kas Keluar): {$ratio_formatted}
        - Jumlah Transaksi Tercatat: {$data['data_count']}

        **Tugas Anda:**
        Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan tajam untuk Direktur Keuangan (CFO).

        Analisis harus fokus pada kesehatan likuiditas dan memberikan rekomendasi keuangan yang bisa ditindaklanjuti.
        1.  **Analisis Kondisi Arus Kas:** Jelaskan secara langsung kondisi arus kas perusahaan. Apakah surplus atau defisit? Seberapa besar? Apa artinya ini bagi kesehatan operasional perusahaan dalam jangka pendek?
        2.  **Evaluasi Rasio Keuangan:** Berikan interpretasi terhadap rasio arus kas. Rasio di atas 1.0 berarti kas masuk lebih besar dari kas keluar. Jelaskan apakah rasio saat ini sehat atau mengkhawatirkan.
        3.  **Rekomendasi Manajemen Keuangan:** Berdasarkan status arus kas (surplus/defisit), berikan 2-3 poin rekomendasi konkret.
            - **Jika Surplus:** Sarankan penggunaan kelebihan dana (misal: 'Alokasikan surplus untuk pembayaran utang lebih awal demi mengurangi beban bunga', 'Investasikan pada instrumen jangka pendek', atau 'Simpan sebagai dana darurat').
            - **Jika Defisit:** Sarankan tindakan perbaikan (misal: 'Segera lakukan penagihan piutang secara agresif', 'Tunda pengeluaran modal (capex) yang tidak mendesak', atau 'Negosiasikan ulang termin pembayaran dengan pemasok').
        4.  **Langkah Selanjutnya:** Berikan saran untuk investigasi lebih lanjut. Contoh: 'Untuk memahami penyebab defisit, perlu dilakukan analisis mendalam pada pos-pos pengeluaran terbesar dalam periode ini.'

        Gunakan format markdown untuk poin-poin agar mudah dibaca.
        PROMPT;
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = ArusKas::where('tanggal', $validatedData['tanggal'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
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
                'tanggal' => 'required|date',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = ArusKas::where('tanggal', $validatedData['tanggal'])
                ->where('id_aruskas', '!=', $aruska->id_aruskas)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Cash Flow Statement|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='gap: 100px; width: 100%;'>
                <div style='width: 45%; float: left; padding-right: 20px;'>
                <h2 style='text-align:center; font-size: 12px; margin: 5px 0;'>Table Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 5px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Cash In (Rp)</th>
                            <th style='border: 1px solid #000; padding: 5px;'>Cash Out (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
                        </div>
                <div style='width: 45%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Chart Cash Flow</h2>
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
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = ArusKas::query();
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

        $aruskass = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Hitung total untuk masing-masing kategori
        $kasMasuk = $aruskass->sum('kas_masuk');
        $kasKeluar = $aruskass->sum('kas_keluar');

        // Format angka menjadi format rupiah atau format angka biasa
        $formattedKasMasuk = number_format($kasMasuk, 0, ',', '.');
        $formattedKasKeluar = number_format($kasKeluar, 0, ',', '.');

        // Siapkan data untuk chart dengan menampilkan nilai
        $chartData = [
            'labels' => [
                "Kas Masuk : Rp $formattedKasMasuk",
                "Kas Keluar : Rp $formattedKasKeluar"
            ],
            'datasets' => [
                [
                    'data' => [$kasMasuk, $kasKeluar],
                    'backgroundColor' => ['#1c64f2', '#ff2323'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#2b6cb0', '#dc2626'],
                ],
            ],
        ];
        return response()->json($chartData);
    }
}
