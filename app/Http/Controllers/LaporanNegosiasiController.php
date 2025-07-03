<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNegosiasi;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LaporanNegosiasiController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanNegosiasi::query()
            ->when($search, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%{$search}%"]));

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allNegotiations = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporannegosiasis = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allNegotiations->pluck('tanggal')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->translatedFormat('F Y'))->all();

        // [FIX] Gunakan kolom 'total_negosiasi'
        $data = $allNegotiations->pluck('total_negosiasi')->all();

        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Laporan Negosiasi',
                'text' => 'Total Negosiasi',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateNegotiationInsight($allNegotiations, $chartData);
        }

        return view('procurements.laporannegosiasi', compact('laporannegosiasis', 'chartData', 'aiInsight'));
    }
    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Negosiasi.
     */
    private function generateNegotiationInsight($negotiationData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($negotiationData->isEmpty()) {
            return 'Tidak ada data negosiasi yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai dengan data "Laporan Negosiasi"
            $analysisData = [
                'periods'              => $chartData['labels'],
                'negotiation_values'   => $chartData['datasets'][0]['data'],
                'total_negotiation'    => $negotiationData->sum('total_negosiasi'),    // Menggunakan 'total_negosiasi'
                'average_negotiation'  => $negotiationData->avg('total_negosiasi'),     // Menggunakan 'total_negosiasi'
                'max_negotiation'      => $negotiationData->max('total_negosiasi'),      // Menggunakan 'total_negosiasi'
                'min_negotiation'      => $negotiationData->min('total_negosiasi'),      // Menggunakan 'total_negosiasi'
                'data_count'           => $negotiationData->count(),
            ];

            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createNegotiationAnalysisPrompt($analysisData);

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
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Negosiasi.
     */
    private function createNegotiationAnalysisPrompt(array $data): string
    {
        $periods = implode(', ', $data['periods']);
        $values  = implode(', ', array_map(fn($v) => 'Rp' . number_format($v, 0, ',', '.'), $data['negotiation_values']));
        $total_negotiation = number_format($data['total_negotiation'], 0, ',', '.');
        $average_negotiation = number_format($data['average_negotiation'], 0, ',', '.');
        $max_negotiation = number_format($data['max_negotiation'], 0, ',', '.');
        $min_negotiation = number_format($data['min_negotiation'], 0, ',', '.');

        return <<<PROMPT
Anda adalah seorang Negosiator Utama dan Manajer Pengadaan (Head of Procurement) yang ahli dalam strategi negosiasi dan penghematan biaya.

Berikut adalah data rekapitulasi nilai total negosiasi yang berhasil dicapai per periode waktu.
- Periode Data: {$periods}
- Rincian Nilai Negosiasi per Periode: {$values}

**Ringkasan Statistik Negosiasi:**
- Total Nilai Negosiasi: Rp {$total_negotiation}
- Rata-rata Nilai Negosiasi per Periode: Rp {$average_negotiation}
- Nilai Negosiasi Tertinggi dalam Satu Periode: Rp {$max_negotiation}
- Nilai Negosiasi Terendah dalam Satu Periode: Rp {$min_negotiation}
- Jumlah Periode Data: {$data['data_count']}

**Tugas Anda:**
Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal untuk C-Level (CEO/CFO).

Analisis harus fokus pada efektivitas dan keberhasilan tim negosiasi.
1.  **Evaluasi Kinerja Negosiasi:** Jelaskan tren dari nilai negosiasi. Apakah tim berhasil mencapai nilai kesepakatan yang lebih tinggi dari waktu ke waktu? Apakah ada pola keberhasilan (misal, akhir kuartal)?
2.  **Identifikasi "Big Wins" & Peluang:** Sebutkan periode dengan nilai negosiasi tertinggi. Berikan interpretasi, contoh: "Nilai negosiasi puncak pada bulan Agustus kemungkinan besar adalah hasil dari kesepakatan strategis dengan vendor utama." Sebutkan juga periode dengan nilai terendah, dan berikan interpretasi, contoh: "Periode dengan nilai rendah mungkin menandakan kurangnya proyek besar atau keberhasilan negosiasi pada kontrak-kontrak kecil."
3.  **Rekomendasi Taktis & Strategis:** Berikan 2-3 poin rekomendasi konkret. Contoh: 'Analisis strategi yang digunakan pada periode 'Big Win' dan terapkan sebagai 'best practice' untuk negosiasi mendatang.' atau 'Fokuskan pelatihan tim pada teknik negosiasi untuk kontrak bernilai menengah, di mana performa tampak stagnan.'
4.  **Dampak terhadap Keuangan:** Berikan pandangan singkat bagaimana hasil negosiasi ini berdampak pada kesehatan finansial perusahaan (misal: penghematan biaya, peningkatan margin, dll.).

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
            //validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_negosiasi' => 'required|integer|min:0'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanNegosiasi::where('tanggal', $validatedData['tanggal'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanNegosiasi::create($validatedData);

            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            Log::error('Error Storing Negosiasi data: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanNegosiasi $laporannegosiasi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_negosiasi' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = LaporanNegosiasi::where('tanggal', $validatedData['tanggal'])
                ->where('id_negosiasi', '!=', $laporannegosiasi->id_negosiasi)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            // Update data
            $laporannegosiasi->update($validatedData);

            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporannegosiasi.index')
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
                ->route('laporannegosiasi.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Negotiation Report|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Negotiation (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Negotiation Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);


            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_negosiasi.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanNegosiasi $laporannegosiasi)
    {
        try {
            $laporannegosiasi->delete();
            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Stok: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanNegosiasiData()
    {
        $data = LaporanNegosiasi::all(['tanggal', 'total_negosiasi']);

        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanNegosiasi::query();
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

        $laporannegosiasis = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        // Siapkan data untuk chart
        $labels = $laporannegosiasis->pluck('tanggal')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('F - Y');
        })->toArray();
        $data = $laporannegosiasis->pluck('total_negosiasi')->toArray();
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
}
