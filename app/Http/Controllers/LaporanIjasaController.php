<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LaporanIjasaController extends Controller
{
    use DateValidationTrait;

 public function index(Request $request)
    {
        $perPage    = $request->input('per_page', 12);
        $query = LaporanIjasa::query();

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

        // Order the results and paginate, ensuring the correct filter parameters are kept.
        $laporanijasas = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));

        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // Pass paginator langsung ke fungsi insight
            $aiInsight = $this->generateReportInsight($laporanijasas);
        }

        return view('hrga.laporanijasa', compact('laporanijasas', 'aiInsight'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'         => 'required|date',
            'jam'             => 'required|date_format:H:i',
            'permasalahan'    => 'required|string',
            'impact'          => 'required|string',
            'troubleshooting' => 'required|string',
            'resolve_tanggal' => 'required|date',
            'resolve_jam'     => 'required|date_format:H:i',
        ]);

        $errorMessage = '';
        if (! $this->isInputAllowed($validated['tanggal'], $errorMessage)) {
            return redirect()->back()->with('error', $errorMessage);
        }

        LaporanIjasa::create($validated);

        return redirect()
            ->route('laporanijasa.index')
            ->with('success', 'Data Berhasil Ditambahkan');
    }

    private function generateReportInsight($rows): string
    {
        // Convert paginator/collection ke array model
        if ($rows instanceof LengthAwarePaginator) {
            $rows = $rows->items();
        } elseif ($rows instanceof Collection) {
            $rows = $rows->all();
        }

        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (! $apiKey || ! $apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if (empty($rows)) {
            return 'Tidak ada data laporan untuk dianalisis.';
        }

        // Susun entri dalam markdown list
        $entriesText = '';
        foreach ($rows as $item) {
            /** @var LaporanIjasa $item */
            $entriesText .= "- **Tanggal & Jam:** " 
                . Carbon::parse($item->tanggal)->format('Y-m-d')
                . " " . $item->jam . "\n"
                . "  - Permasalahan: {$item->permasalahan}\n"
                . "  - Impact: {$item->impact}\n"
                . "  - Troubleshooting: {$item->troubleshooting}\n"
                . "  - Tanggal & Jam Selesai: " 
                . Carbon::parse($item->resolve_tanggal)->format('Y-m-d')
                . " {$item->resolve_jam}\n\n";
        }

        $prompt = $this->createReportPrompt($entriesText);

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($apiUrl . '?key=' . $apiKey, [
                    'contents' => [[
                        'parts' => [['text' => $prompt]]
                    ]],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text']
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
     * Buat prompt AI berdasar entri laporan.
     */
    private function createReportPrompt(string $entriesText): string
    {
        return <<<PROMPT
            Anda adalah seorang analis TI senior di sebuah perusahaan di Indonesia.
            Berikut data laporan insiden & troubleshooting harian:

            {$entriesText}
            Tugas Anda:
            1. **Ringkasan Umum**: Gambarkan tren frekuensi dan jenis permasalahan (misal: banyak gangguan jaringan, error sistem, dsb.).
            2. **Analisis Dampak**: Identifikasi 2–3 kasus dengan impact tertinggi; jelaskan faktor penyebab yang menonjol.
            3. **Evaluasi Troubleshooting**: Tinjau efektivitas langkah troubleshooting; sebutkan 2 langkah paling berhasil dan 2 yang perlu diperbaiki.
            4. **Rekomendasi & Proyeksi**: Berikan minimal 3 rekomendasi teknis/operasional untuk mencegah insiden serupa dan proyeksi kualitatif untuk periode berikutnya.

            Gunakan bahasa Indonesia formal, maksimal 5 paragraf. Gunakan format markdown untuk poin-poin.
            PROMPT;
        }
    public function update(Request $request, LaporanIjasa $laporanijasa)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'nullable|date_format:H:i',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            
            $laporanijasa->update($validatedData);

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function exportPDF(Request $request)
{
    try {
        $data = $request->validate([
            'table' => 'required|string',
        ]);

        $tableHTML = trim($data['table']);

        if (empty($tableHTML)) {
            return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
        }

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L', // Landscape orientation
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35, // Tambahkan margin atas untuk header teks
            'margin_bottom' => 20, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - iJASA Report|');

         // Set CSS untuk memastikan formatting CKEditor dipertahankan
         $styleCSS = "
         ul, ol {
             padding-left: 20px;
             margin: 5px 0;
         }
         li {
             margin-bottom: 3px;
         }
         p {
             margin: 5px 0;
         }
         strong, b {
             font-weight: bold;
         }
         em, i {
             font-style: italic;
         }
     ";
     
     // Buat konten tabel dengan style tambahan untuk CKEditor
     $htmlContent = "
         <style>
             {$styleCSS}
         </style>
            <div style='width: 100%;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Hours</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Problem</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Impact</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Troubleshooting</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Date</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
        ";

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_ijasa.pdf"');
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}

    public function destroy(LaporanIjasa $laporanijasa)
    {

            $laporanijasa->delete();

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Dihapus');
 
    }
}
