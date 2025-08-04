<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanDetrans;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;


class LaporanDetransController extends Controller
{
    use DateValidationTrait;


    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanDetrans::query()
            ->when($search, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"]));

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allDeliveryReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporandetrans = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Logika Chart diperbaiki untuk menangani time-series per pelaksana
        $months = $allDeliveryReports->map(function ($item) {
            return Carbon::parse($item->tanggal)->translatedFormat('F Y');
        })->unique()->values();

        $groupedData = $allDeliveryReports->groupBy('pelaksana');

        $colorMap = [
            'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 99, 132, 0.7)',
            'Pengiriman Luar Daerah (DETRANS)' => 'rgba(54, 162, 235, 0.7)',
        ];
        $defaultColor = 'rgba(201, 203, 207, 0.7)';

        $datasets = [];
        foreach ($groupedData as $pelaksana => $reports) {
            $data = $months->map(function ($month) use ($reports) {
                return $reports->filter(function ($report) use ($month) {
                    return Carbon::parse($report->tanggal)->translatedFormat('F Y') === $month;
                })->sum('total_pengiriman');
            });

            $datasets[] = [
                'label' => $pelaksana,
                'data' => $data,
                'backgroundColor' => $colorMap[$pelaksana] ?? $defaultColor,
                'borderColor' => str_replace('0.7', '1', $colorMap[$pelaksana] ?? $defaultColor),
                'borderWidth' => 1,
            ];
        }

        $chartData = [
            'labels' => $months,
            'datasets' => $datasets,
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateDeliveryInsight($allDeliveryReports, $chartData);
        }

        return view('supports.laporandetrans', compact('laporandetrans', 'chartData', 'aiInsight'));
    }

    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Pengiriman.
     */
    private function generateDeliveryInsight($deliveryData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($deliveryData->isEmpty()) {
            return 'Tidak ada data pengiriman yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai
            $analysisData = [
                'time_periods'      => $chartData['labels'],
                'datasets'          => $chartData['datasets'],
                'total_deliveries'  => $deliveryData->sum('total_pengiriman'),
                'average_deliveries' => $deliveryData->avg('total_pengiriman'),
                'max_deliveries'    => $deliveryData->max('total_pengiriman'),
                'min_deliveries'    => $deliveryData->min('total_pengiriman'),
                'data_count'        => $deliveryData->count(),
                // [BARU] Menambahkan data agregat per pelaksana untuk analisis perbandingan
                'deliveries_per_executor' => $deliveryData->groupBy('pelaksana')->map->sum('total_pengiriman')->all(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createDeliveryAnalysisPrompt($analysisData);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 1024],
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
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Pengiriman.
     */
    private function createDeliveryAnalysisPrompt(array $data): string
    {
        $total_deliveries = number_format($data['total_deliveries'], 0, ',', '.');

        $executorValuesStr = '';
        foreach ($data['deliveries_per_executor'] as $executor => $total) {
            $executorValuesStr .= "- **{$executor}**: " . number_format($total, 0, ',', '.') . " total pengiriman\n";
        }

        // Membuat ringkasan data per dataset untuk prompt
        $datasetsStr = '';
        foreach ($data['datasets'] as $dataset) {
            $datasetsStr .= "- Dataset '{$dataset['label']}': " . implode(', ', $dataset['data']) . "\n";
        }

        return <<<PROMPT
Anda adalah seorang Manajer Logistik dan Operasional yang sangat ahli dalam menganalisis data pengiriman.

Berikut adalah data rekapitulasi jumlah pengiriman barang, yang dibagi berdasarkan pelaksana: Pengiriman Lokal (SAMITRA) dan Pengiriman Luar Daerah (DETRANS).
- Periode Waktu: {$data['time_periods']->implode(', ')}

**Data Pengiriman per Bulan untuk Setiap Pelaksana:**
{$datasetsStr}

**Ringkasan Statistik Total:**
- Total Semua Pengiriman: {$total_deliveries}
- Rata-rata Jumlah Pengiriman per Laporan: {$data['average_deliveries']}

**Akumulasi Total per Pelaksana:**
{$executorValuesStr}

**Tugas Anda:**
Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk kepala operasional.

Analisis harus fokus pada perbandingan kinerja dan tren antara pengiriman lokal dan luar daerah.
1.  **Analisis Perbandingan Kinerja:** Bandingkan volume pengiriman antara 'Pengiriman Daerah Bali (SAMITRA)' dan 'Pengiriman Luar Daerah (DETRANS)'. Pelaksana mana yang menangani volume lebih besar secara keseluruhan?
2.  **Analisis Tren Temporal:** Jelaskan tren pengiriman untuk masing-masing pelaksana dari waktu ke waktu. Apakah ada bulan-bulan tertentu di mana pengiriman lokal melonjak? Atau kapan pengiriman luar daerah mencapai puncaknya? Berikan hipotesis (misal: "Peningkatan pengiriman luar daerah pada akhir tahun sejalan dengan musim liburan.").
3.  **Rekomendasi Operasional & Alokasi Armada:** Berdasarkan tren, berikan 2-3 poin rekomendasi konkret. Contoh: 'Melihat puncak pengiriman DETRANS di Q4, rencanakan penambahan armada atau staf sementara untuk periode yang sama tahun depan.' atau 'Volume pengiriman SAMITRA yang stabil memungkinkan untuk fokus pada optimasi rute demi efisiensi bahan bakar.'
4.  **Proyeksi Kebutuhan:** Berikan proyeksi kualitatif singkat mengenai kebutuhan operasional untuk kuartal berikutnya berdasarkan data historis ini.

Gunakan format markdown untuk poin-poin agar mudah dibaca.
PROMPT;
    }

    public function store(Request $request)
    {
        try {
            //validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanDetrans::where('tanggal', $validatedData['tanggal'])
                ->where('pelaksana', $validatedData['pelaksana'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }

            LaporanDetrans::create($validatedData);

            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Detrans Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanDetrans $laporandetran)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanDetrans::where('tanggal', $validatedData['tanggal'])
                ->where('pelaksana', $validatedData['pelaksana'])
                ->where('id_detrans', '!=', $laporandetran->id_detrans)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
            }

            // Update data
            $laporandetran->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()->route('laporandetrans.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Data: ' . $e->getMessage());
            return redirect()
                ->route('laporandetrans.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Supports - Shipping Report Recap|');

            // Konten HTML
            $htmlContent = "
        <div style='gap: 100px; width: 100%;'>
            <div style='width: 30%; float: left; padding-right: 20px;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Executor</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Total Shipping (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
            <div style='width: 65%; text-align:center; margin-left: 20px;'>
                <h2 style='font-size: 14px; margin-bottom: 10px;'>Shipping Chart</h2>
                <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Penjualan' />
            </div>
        </div>
        ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_pengiriman_detrans.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanDetrans $laporandetran)
    {
        try {
            $laporandetran->delete();
            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Detrans Data Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanSamitraData()
    {
        $data = LaporanDetrans::all(['tanggal', 'total_pengiriman']);

        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanDetrans::query();
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

        $laporandetrans = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Ambil semua bulan yang ada dalam data
        $months = $laporandetrans->sortBy('tanggal')->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
        })->unique()->values()->toArray();

        // Kelompokkan data berdasarkan pelaksana dan bulan tanpa akumulasi
        $groupedData = [];
        foreach ($laporandetrans as $item) {
            $month = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            $groupedData[$item->pelaksana][$month][] = $item->total_pengiriman; // Simpan sebagai array
        }

        // Siapkan warna untuk setiap pelaksana
        $colorMap = [
            'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
            'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
        ];
        $defaultColor = 'rgba(128, 128, 128, 0.7)';

        // Bangun datasets tanpa akumulasi
        $datasets = [];
        foreach ($groupedData as $pelaksana => $monthData) {
            $data = [];
            foreach ($months as $month) {
                $data[] = isset($monthData[$month]) ? array_sum($monthData[$month]) : 0; // Tidak akumulasi, hanya total per bulan
            }

            $datasets[] = [
                'label' => $pelaksana,
                'data' => $data,
                'backgroundColor' => $colorMap[$pelaksana] ?? $defaultColor,
            ];
        }

        $chartData = [
            'labels' => $months,
            'datasets' => $datasets,
        ];

        // Kembalikan data dalam format JSON
        return response()->json($chartData);
    }
}
