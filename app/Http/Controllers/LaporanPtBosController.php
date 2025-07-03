<?php

namespace App\Http\Controllers;

use App\Models\LaporanPtBos;
use App\Traits\DateValidationTrait;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class LaporanPtBosController extends Controller
{
    use DateValidationTrait;

     public function index(Request $request)
    {
        $perPage    = $request->input('per_page', 12);
        $search     = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth   = $request->input('end_month');

        $query = LaporanPtBos::query();

        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }

        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate   = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'Format tanggal tidak valid. Gunakan format Y-m.'
                ], 400);
            }
        }

        $laporanptboss = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // Kita pass langsung paginator, fungsi akan meng-handle conversion
            $aiInsight = $this->generateReportInsight($laporanptboss);
        }

        return view('hrga.laporanptbos', compact('laporanptboss', 'aiInsight'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'               => 'required|date',
            'pekerjaan'             => 'required|string',
            'kondisi_bulanlalu'     => 'required|string',
            'kondisi_bulanini'      => 'required|string',
            'update'                => 'required|string',
            'rencana_implementasi'  => 'required|string',
            'keterangan'            => 'required|string',
        ]);

        $errorMessage = '';
        if (! $this->isInputAllowed($validated['tanggal'], $errorMessage)) {
            return redirect()->back()->with('error', $errorMessage);
        }

        LaporanPtBos::create($validated);

        return redirect()
            ->route('laporanptbos.index')
            ->with('success', 'Data Berhasil Ditambahkan');
    }

    /**
     * Mengirim data laporan ke Gemini API untuk menghasilkan insight.
     * Bisa menerima LengthAwarePaginator, Collection, atau array.
     *
     * @param  mixed  $rows
     * @return string
     */
    private function generateReportInsight($rows): string
    {
        // Jika paginator, ambil items() → array
        if ($rows instanceof LengthAwarePaginator) {
            $rows = $rows->items();
        }
        // Jika collection, ubah ke array
        elseif ($rows instanceof Collection) {
            $rows = $rows->all();
        }
        // jika sudah array, biarkan saja

        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (! $apiKey || ! $apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if (empty($rows)) {
            return 'Tidak ada data laporan untuk dianalisis.';
        }

        // Susun entri laporan dalam markdown list
        $entriesText = '';
        foreach ($rows as $item) {
            /** @var LaporanPtBos $item */
            $entriesText .= "- **Tanggal:** " . Carbon::parse($item->tanggal)->format('Y-m-d') . "\n"
                          . "  - Pekerjaan: {$item->pekerjaan}\n"
                          . "  - Kondisi Bulan Lalu: {$item->kondisi_bulanlalu}\n"
                          . "  - Kondisi Bulan Ini: {$item->kondisi_bulanini}\n"
                          . "  - Update: {$item->update}\n"
                          . "  - Rencana Implementasi: {$item->rencana_implementasi}\n"
                          . "  - Keterangan: {$item->keterangan}\n\n";
        }

        $prompt = $this->createReportPrompt($entriesText);

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl . '?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari AI.';
            } else {
                Log::error('Gemini API error: ' . $response->body());
                return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
            }
        } catch (Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * Buat prompt untuk AI berdasarkan teks entri laporan.
     *
     * @param  string  $entriesText
     * @return string
     */
    private function createReportPrompt(string $entriesText): string
    {
    return <<<PROMPT
        Anda adalah seorang analis bisnis senior di sebuah perusahaan di Indonesia.
        Berikut adalah data laporan bulanan yang telah diinput oleh tim HRGA:

        {$entriesText}
        Tugas Anda:
        1. **Ringkasan Umum**: Berikan gambaran singkat mengenai tren keseluruhan (apakah ada perbaikan, stagnasi, atau penurunan), berdasarkan kumpulan laporan di atas.
        2. **Sorotan Utama**: Identifikasi 2–3 entri dengan kondisi terbaik dan 2–3 entri dengan kondisi terburuk; jelaskan kemungkinan penyebab (misalnya kendala operasional, inisiatif baru, dll.).
        3. **Rekomendasi Strategis**: Berikan minimal 3 poin rekomendasi yang konkret dan dapat ditindaklanjuti untuk perbaikan di periode berikutnya.
        4. **Tindak Lanjut & Proyeksi**: Sarankan langkah-langkah tindak lanjut dan prediksi kualitatif untuk periode laporan selanjutnya.

        Gunakan bahasa Indonesia yang formal dan profesional, maksimal 5 paragraf. Gunakan format markdown untuk poin-poin agar mudah dibaca.
        PROMPT;
    }
            

    public function update(Request $request, LaporanPtBos $laporanptbo)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pekerjaan' => 'required|string',
                'kondisi_bulanlalu' => 'required|string',
                'kondisi_bulanini' => 'required|string',
                'update' => 'required|string',
                'rencana_implementasi' => 'required|string',
                'keterangan' => 'required|string'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            $laporanptbo->update($validatedData);
    
            return redirect()->route('laporanptbos.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating PT BOS Data: ' . $e->getMessage());
            return redirect()->route('laporanptbos.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
            ]);
    
            // Ambil data dari request
            $tableHTML = trim($data['table']);
    
            // Validasi isi tabel untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - PT BOS Report|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Work</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Last Month Condition</th>
                            <th style='border: 1px solid #000; padding: 2px;'>This Month Condition</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Update</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Implementation Plan</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
            ";
    
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_PT_BOS.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(LaporanPtBos $laporanptbo)
    {
        $laporanptbo->delete();

        return redirect()->route('laporanptbos.index')->with('success', 'Data Berhaisil Dihapus');
    }

}
