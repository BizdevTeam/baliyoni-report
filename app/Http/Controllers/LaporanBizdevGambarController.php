<?php

namespace App\Http\Controllers;

use App\Models\LaporanBizdevGambar;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class LaporanBizdevGambarController extends Controller
{
    use DateValidationTrait;

     public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $query = LaporanBizdevGambar::query();

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
        $laporanbizdevgambars = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));

        // Ubah path gambar
        $laporanbizdevgambars->getCollection()->transform(function ($item) {
            $path = public_path("images/it/laporanbizdevgambar/{$item->gambar}");
            $item->gambar_url = $item->gambar && file_exists($path)
                ? asset("images/it/laporanbizdevgambar/{$item->gambar}")
                : asset("images/no-image.png");
            return $item;
        });

        // AI Insight: hanya jika ada query generate_ai dan bukan AJAX
        $aiInsight = null;
        if (! $request->ajax() && $request->has('generate_ai')) {
            $aiInsight = $this->generateBizdevImageInsight($laporanbizdevgambars);
        }

        if ($request->ajax()) {
            return response()->json(['laporanbizdevgambars' => $laporanbizdevgambars]);
        }

        return view('it.laporanbizdevgambar', compact('laporanbizdevgambars', 'aiInsight'));
    }
  private function generateBizdevImageInsight($rows): string
    {
        // 1. Convert paginator/collection ke array
        if ($rows instanceof LengthAwarePaginator) {
            $rows = $rows->items();
        } elseif ($rows instanceof Collection) {
            $rows = $rows->all();
        }

        // 2. Setup API
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');
        if (! $apiKey || ! $apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }
        if (empty($rows)) {
            return 'Tidak ada data kendala untuk dianalisis.';
        }

        // 3. Siapkan teks deskripsi kendala per tanggal
        $kendalaLines = [];
        foreach ($rows as $item) {
            $date = Carbon::parse($item->tanggal)->format('Y-m-d');
            $kendalaLines[] = "• {$date}: {$item->kendala}";
        }
        $kendalaText = implode("\n", $kendalaLines);

        // 4. Kumpulkan inline gambar
        $imageParts = [];
        foreach ($rows as $item) {
            if (empty($item->gambar)) continue;
            $path = public_path("images/it/laporanbizdevgambar/{$item->gambar}");
            if (file_exists($path) && is_readable($path)) {
                $imageParts[] = [
                    'inline_data' => [
                        'mime_type' => mime_content_type($path),
                        'data'      => base64_encode(file_get_contents($path)),
                    ]
                ];
            }
        }
        if (empty($imageParts)) {
            return 'Tidak ada file gambar yang valid untuk dianalisis.';
        }

        // 5. Buat prompt akhir
        $textPrompt = $this->createFormattedBizdevPrompt($kendalaText, count($imageParts));

        // 6. Gabungkan teks + gambar, kirim ke API
        $payloadParts = array_merge(
            [['text' => $textPrompt]],
            $imageParts
        );

        try {
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl . '?key=' . $apiKey, [
                    'contents' => [['parts' => $payloadParts]],
                    'generationConfig' => [
                        'temperature'     => 0.4,
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari gambar.';
            }

            Log::error('Gemini Vision API error: ' . $response->body());
            return 'Gagal menghubungi layanan AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating image AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan saat menghasilkan analisis gambar.';
        }
    }

    /**
     * Buat prompt dinamis sesuai data kendala & jumlah gambar
     */
    private function createFormattedBizdevPrompt(string $kendalaText, int $imageCount): string
    {
        return <<<PROMPT
            Anda adalah Analis BizDev dan Ahli Strategi Produk senior.  
            Saya telah mengirim **{$imageCount} gambar screenshot** dari modul‐modul dan progress mengenai aplikasi kami, beserta ringkasan kendala harian:

            {$kendalaText}

            TUGAS ANDA:
            1. **Ekstrak & Klasifikasi Kendala:** Dari deskripsi di atas dan gambar, identifikasi tipe‐tipe kendala (misal: workflow pending, bug UI, inkonsistensi status, performa lambat).
            2. **Analisis Dampak:** Jelaskan bagaimana setiap kategori kendala memengaruhi progress dan timeline proyek.
            3. **Rekomendasi Prioritas:** Beri 3–5 rekomendasi konkret untuk memperbaiki alur kerja, UI/UX, dan performa, urutkan berdasarkan urgensi.
            4. **Langkah Selanjutnya:** Sarankan tahapan implementasi (milestone) untuk menyelesaikan perbaikan dalam 1–2 sprint ke depan.

            Gunakan bahasa Indonesia formal, susun output dalam markdown dengan:
            - Ringkasan Umum (1 paragraf)
            - Klasifikasi Kendala (poin‐poin)
            - Rekomendasi (poin‐poin)
            - Langkah Selanjutnya (1 paragraf)
            PROMPT;
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kendala' => 'required|string',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/laporanbizdevgambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            LaporanBizdevGambar::create($validatedData);

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanBizdevGambar $laporanbizdevgambar)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kendala' => 'required|string',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $destination = "images/it/laporanbizdevgambar/" . $laporanbizdevgambar->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/laporanbizdevgambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            $laporanbizdevgambar->update($validatedData);

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanBizdevGambar $laporanbizdevgambar)
    {
        try {
            $destination = "images/it/laporanbizdevgambar/" . $laporanbizdevgambar->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $laporanbizdevgambar->delete();

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
{
    try {
        // Validasi input date
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
        ]);

        // Ambil semua data laporan berdasarkan date yang dipilih
        $laporans = LaporanBizdevGambar::where('tanggal', $validatedData['tanggal'])->get();

        if ($laporans->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Inisialisasi mPDF
        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35,
            'margin_bottom' => 20,
            'format' => 'A4',
        ]);

        // Tambahkan header
        $headerImagePath = public_path('images/HEADER.png');
        $mpdf->SetHTMLHeader("<div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
            <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
        </div>", 'O');

        // Tambahkan footer
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Business Development Report|');

        // Loop melalui setiap laporan dan tambahkan ke PDF
        foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->gambar) && file_exists(public_path("images/it/laporanbizdevgambar/{$laporan->gambar}"))) {
                $imagePath = public_path("images/it/laporanbizdevgambar/{$laporan->gambar}");
                $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; text-align:center;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>File not found</p>";
            }

            // Konten untuk setiap laporan
            $htmlContent = "
                <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                    {$imageHTML}
                </div>
            ";
            
            // Tambahkan ke PDF
            $mpdf->WriteHTML($htmlContent);
        }

        // Tambahkan halaman baru untuk tabel kendala dan tanggal
        $mpdf->AddPage();
        $tableContent = "<h2 style='text-align: center;'>Diffculty List</h2>
            <table border='1' style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th style='padding: 8px; background-color: #f2f2f2;'>Diffculty</th>
                        <th style='padding: 8px; background-color: #f2f2f2;'>Date</th>
                    </tr>
                </thead>
                <tbody>";
        
        foreach ($laporans as $laporan) {
            $tableContent .= "<tr>
                <td style='padding: 8px;'>" . $laporan->tanggal_formatted . "</td>
                <td style='padding: 8px;'>" . $laporan->kendala . "</td>
            </tr>";
        }

        $tableContent .= "</tbody></table>";
        $mpdf->WriteHTML($tableContent);

        // Output PDF
        return response($mpdf->Output("laporan_bizdev_gambar_{$validatedData['tanggal']}.pdf", 'D'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_bizdev_gambar.pdf"');
    
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}

}
