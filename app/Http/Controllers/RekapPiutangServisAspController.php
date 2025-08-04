<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPiutangServisAsp;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Http;

class RekapPiutangServisAspController extends Controller
{
    use DateValidationTrait;
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $query = RekapPiutangServisAsp::query();

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allReceivables = (clone $query)->orderBy('nilai_piutang', 'desc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $rekappiutangservisasps = (clone $query)
        ->orderBy('nilai_piutang', 'desc')
        ->paginate($perPage)
        ->appends($request->only(['start_date', 'end_date', 'per_page']));


        // Warna tetap untuk setiap pelaksana
        $pelaksanaColors = [
            'CV. ARI DISTRIBUTION CENTER' => 'rgba(255, 99, 132, 0.7)',
            'CV. BALIYONI COMPUTER' => 'rgba(54, 162, 235, 0.7)',
            'PT. NABA TECHNOLOGY SOLUTIONS' => 'rgba(255, 206, 86, 0.7)',
            'CV. ELKA MANDIRI (50%)-SAMITRA' => 'rgba(75, 192, 192, 0.7)',
            'CV. ELKA MANDIRI (50%)-DETRAN' => 'rgba(153, 102, 255, 0.7)'
        ];

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allReceivables->map(function ($item) {
            return $item->pelaksana . ' (Rp ' . number_format($item->nilai_piutang) . ')';
        })->all();

        // [FIX] Gunakan kolom 'nilai_piutang'
        $data = $allReceivables->pluck('nilai_piutang')->all();

        $backgroundColors = $allReceivables->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(201, 203, 207, 0.7)')->all();

        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Rekap Piutang Servis ASP',
                'text' => 'Nilai Piutang Servis ASP',
                'data' => $data,
                'backgroundColor' => $backgroundColors,
            ]],
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateReceivablesInsight($allReceivables, $chartData);
        }

        return view('supports.rekappiutangservisasp', compact('rekappiutangservisasps', 'chartData', 'aiInsight'));
    }
    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Piutang (Receivables).
     */
    private function generateReceivablesInsight($receivablesData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            // ... (error handling)
        }

        if ($receivablesData->isEmpty()) {
            return 'Tidak ada data piutang yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai
            $analysisData = [
                'executors'           => $chartData['labels'],
                'receivable_values'   => $chartData['datasets'][0]['data'],
                'total_receivables'   => $receivablesData->sum('nilai_piutang'),    // Menggunakan 'nilai_piutang'
                'average_receivables' => $receivablesData->avg('nilai_piutang'),     // Menggunakan 'nilai_piutang'
                'max_receivables'     => $receivablesData->max('nilai_piutang'),      // Menggunakan 'nilai_piutang'
                'min_receivables'     => $receivablesData->min('nilai_piutang'),      // Menggunakan 'nilai_piutang'
                'data_count'          => $receivablesData->count(),
                // [BARU] Menambahkan data agregat per pelaksana untuk analisis risiko
                'receivables_per_executor' => $receivablesData->groupBy('pelaksana')->map->sum('nilai_piutang')->all(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createReceivablesAnalysisPrompt($analysisData);

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
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Analisis Piutang.
     */
    private function createReceivablesAnalysisPrompt(array $data): string
    {
        $total_receivables = number_format($data['total_receivables'], 0, ',', '.');

        $executorValuesStr = '';
        arsort($data['receivables_per_executor']); // Urutkan dari piutang terbesar
        foreach ($data['receivables_per_executor'] as $executor => $total) {
            $executorValuesStr .= "- **{$executor}**: Rp " . number_format($total, 0, ',', '.') . "\n";
        }

        return <<<PROMPT
Anda adalah seorang Manajer Keuangan (Finance Manager) yang fokus pada manajemen piutang (accounts receivable) dan kesehatan arus kas (cash flow).

Berikut adalah data rekapitulasi nilai piutang yang belum tertagih dari masing-masing pelaksana servis (ASP). Perlu diingat, angka yang tinggi menunjukkan risiko yang lebih besar.
- Total Piutang Belum Tertagih: Rp {$total_receivables}
- Jumlah Total Laporan Piutang: {$data['data_count']}

**Rincian Piutang per Pelaksana (Diurutkan dari Terbesar):**
{$executorValuesStr}

**Tugas Anda:**
Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk Direktur Keuangan (CFO).

Analisis harus fokus pada identifikasi risiko kredit dan rekomendasi penagihan.
1.  **Analisis Kesehatan Piutang:** Identifikasi 3 pelaksana dengan nilai piutang tertinggi. Jelaskan mengapa ini menjadi fokus utama dan risiko terbesar bagi arus kas perusahaan.
2.  **Identifikasi Risiko Konsentrasi:** Berikan komentar mengenai distribusi piutang. Apakah piutang terbesar terkonsentrasi hanya pada beberapa pelaksana? Jelaskan risikonya jika salah satu dari mereka gagal bayar.
3.  **Rekomendasi Penagihan (Collection):** Berikan 2-3 poin rekomendasi yang sangat konkret dan mendesak. Contoh: 'Segera kirimkan surat peringatan dan lakukan follow-up intensif via telepon kepada CV. ARI DISTRIBUTION CENTER yang memiliki piutang tertinggi.' atau 'Pertimbangkan untuk menahan sementara layanan baru untuk PT. NABA TECHNOLOGY SOLUTIONS sampai ada pembayaran yang masuk.'
4.  **Rekomendasi Kebijakan Kredit:** Sarankan evaluasi kebijakan. Contoh: 'Tinjau ulang batas kredit (credit limit) dan termin pembayaran (payment terms) untuk pelaksana dengan riwayat piutang yang konsisten tinggi.'

Gunakan format markdown untuk poin-poin agar mudah dibaca.
PROMPT;
    }
    public function store(Request $request)
    {
        try {
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
                'nilai_piutang' => 'required|integer|min:0',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPiutangServisAsp::where('tanggal', $validatedData['tanggal'])
                ->where('pelaksana', $validatedData['pelaksana'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }

            RekapPiutangServisAsp::create($validatedData);

            return redirect()->route('rekappiutangservisasp.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Rekap Pendapatan Servis ASP Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('rekappiutangservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPiutangServisAsp $rekappiutangservisasp)
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
                'nilai_piutang' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = RekapPiutangServisAsp::where('tanggal', $validatedData['tanggal'])
                ->where('pelaksana', $validatedData['pelaksana'])
                ->where('id_rpiutangsasp', '!=', $rekappiutangservisasp->id_rpiutangsasp)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
            }

            // Update data
            $rekappiutangservisasp->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()
                ->route('rekappiutangservisasp.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPiutangServisAsp::where('tanggal', $validatedData['tanggal'])
                ->where('pelaksana', $validatedData['pelaksana'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Update data rekappiutang
            $rpiutangsasp->update($validatedData);

            return redirect()->route('rpiutangsasp.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()
                ->route('rekappiutangservisasp.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Support - ASP Service Receivables Recap|');

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
                                <th style='border: 1px solid #000; padding: 2px;'>Receivables Value (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>ASP Service Receivables Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
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

    public function destroy(RekapPiutangServisAsp $rekappiutangservisasp)
    {
        try {
            $rekappiutangservisasp->delete();
            return redirect()->route('rekappiutangservisasp.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Pendapatan Servis ASP Data: ' . $e->getMessage());
            return redirect()->route('rekappiutangservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function getRekapPenjualaPerusahaannData()
    {
        $data = RekapPiutangServisAsp::all(['tanggal', 'pelaksana', 'nilai_piutang']);

        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = RekapPiutangServisAsp::query();
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

        $rekappiutangservisasps = $query
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
        $labels = $rekappiutangservisasps->map(function ($item) {
            return $item->pelaksana . ' (' . 'Rp' . ' ' . number_format($item->nilai_piutang) . ')';
        })->toArray();
        $data = $rekappiutangservisasps->pluck('nilai_piutang')->toArray(); // Nilai pendapatan

        $backgroundColors = $rekappiutangservisasps->map(fn($item) => $pelaksanaColors[$item->pelaksana] ?? 'rgba(0, 0, 0, 0.7)')->toArray();

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
        $query = RekapPiutangServisAsp::query();

        if ($search) {
            $query->where('tanggal', 'LIKE', "%$search%");
        }

        if ($startMonth && $endMonth) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Ambil data yang sudah difilter
        $rekappiutangservisasps = $query->get();

        // Akumulasi total piutang berdasarkan pelaksana
        $akumulasiData = $rekappiutangservisasps->groupBy('pelaksana')->map(fn($items) => $items->sum('nilai_piutang'));

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
