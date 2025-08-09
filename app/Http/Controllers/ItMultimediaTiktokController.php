<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItMultimediaTiktok;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class ItMultimediaTiktokController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $query = ItMultimediaTiktok::query();

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
        $itmultimediatiktoks = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));
            
        // Ubah path gambar agar dapat diakses dari frontend
        $itmultimediatiktoks->getCollection()->transform(function ($item) {
        $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/it/multimediatiktok/{$item->gambar}"))
            ? asset("images/it/multimediatiktok/{$item->gambar}")
            : asset("images/no-image.png");
                  return $item;
    }); // Placeholder jika tidak ada gambar

   // --- AI INSIGHT: JALANKAN HANYA KETIKA generate_ai ada di URL ---
    $aiInsight = null;
    if (!$request->ajax() && $request->has('generate_ai')) {
        // ambil collection (halaman saat ini)
        $postsToAnalyze = $itmultimediatiktoks->getCollection();
        // panggil fungsi analisis batch gambar
        $aiInsight = $this->generateImageBatchAnalysis($postsToAnalyze);
    }

    // AJAX response tetap seperti semula
    if ($request->ajax()) {
        return response()->json([
            'itmultimediatiktoks' => $itmultimediatiktoks
        ]);
    }

        return view('it.mutimediatiktok', compact('itmultimediatiktoks','aiInsight'));
    }

    private function generateImageBatchAnalysis($tiktokPosts)
    {
        // 1. Setup Kredensial API
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($tiktokPosts->isEmpty()) {
            return 'Tidak ada postingan untuk dianalisis.';
        }

        try {
            // 2. Kumpulkan semua gambar yang valid
            $imageParts = [];
            foreach ($tiktokPosts as $post) {
                // Lewati jika nama file gambar kosong
                if (empty($post->gambar)) {
                    continue;
                }

                $imagePath = public_path("images/it/multimediatiktok/{$post->gambar}");

                // Hanya proses gambar jika file ada dan bisa dibaca
                if (file_exists($imagePath) && is_readable($imagePath)) {
                    $imageParts[] = [
                        'inline_data' => [
                            'mime_type' => mime_content_type($imagePath),
                            'data'      => base64_encode(file_get_contents($imagePath))
                        ]
                    ];
                }
            }

            // 3. Pastikan ada gambar yang bisa dianalisis
            if (empty($imageParts)) {
                return 'Tidak ada file gambar yang valid untuk dianalisis pada rentang data ini.';
            }

            // 4. Hitung jumlah gambar valid, LALU buat prompt dinamis
            $validImageCount = count($imageParts);
            $textPrompt = $this->createFormattedReportPrompt($validImageCount);
            
            // 5. Gabungkan prompt teks dengan data gambar
            $payloadContents = array_merge([['text' => $textPrompt]], $imageParts);

            // 6. Kirim request ke API Gemini
            $response = Http::timeout(120)->withHeaders([ // Timeout 120 detik
                'Content-Type' => 'application/json',
            ])->post($apiUrl . '?key=' . $apiKey, [
                'contents' => [['parts' => $payloadContents]],
                'generationConfig' => [
                    'temperature'     => 0.4,
                    'maxOutputTokens' => 4096,
                ]
            ]);

            // 7. Proses respons dari API
            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari gambar.';
            } else {
                Log::error('Gemini Vision API error: ' . $response->body());
                return 'Gagal menghubungi layanan analisis AI. Cek log untuk detail.';
            }

        } catch (\Exception $e) {
            Log::error('Error generating image AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan sistem saat menghasilkan analisis gambar.';
        }
    }

     private function createFormattedReportPrompt(int $imageCount)
    {
       return "Anda adalah seorang Analis Data dan Ahli Strategi Media Sosial senior. Anda sangat ahli dalam membaca data dari gambar laporan dan menerjemahkannya menjadi insight strategis.

        **TUGAS ANDA:**
        Saya telah memberikan Anda **{$imageCount} gambar** dari laporan analitik Instagram.
        1.  **Ekstrak Data:** Pertama, baca dan ekstrak semua data kuantitatif (angka dan persentase) dari gambar laporan yang saya berikan.
        2.  **Isi & Analisis:** Kedua, gunakan data yang Anda ekstrak tersebut untuk **mengisi semua placeholder** seperti `[angka]` atau `[%]` dalam template laporan di bawah ini.
        3.  **Berikan Wawasan:** Ketiga, selain mengisi angka, berikan juga analisis kualitatif (penjelasan 'mengapa') untuk setiap poin berdasarkan data yang Anda lihat.

        Output akhir harus berupa laporan yang lengkap dengan data dan analisis.

        ---
        **TEMPLATE LAPORAN (Baca gambar, lalu isi dan lengkapi template ini):**

        ### Analisis Kinerja & Rencana Tindak Lanjut (Periode [Tanggal Awal] - [Tanggal Akhir])

        **Problem:**
        - **Kualitas Engagement Rendah:** Meskipun mendapatkan [angka] Views, interaksi yang dihasilkan relatif rendah (hanya [angka] interaksi dari [angka] akun). Ini menunjukkan konten gagal mengubah penonton menjadi partisipan aktif. *[Dari analisis gambar, jelaskan MENGAPA konten visual ini cenderung pasif dan tidak memancing komentar.]*
        - **Jangkauan Non-Followers Rendah/Nihil:** Akun @balimall.id kesulitan menjangkau audiens di luar pengikutnya ([%]% engagement dari non-followers), sangat kontras dengan performa @baliyonigroup yang berhasil meraih [%]%. *[Analisis gaya visual @balimall.id dari gambar. Apakah terlalu umum, kurang unik, atau tidak memiliki 'wow-factor' sehingga tidak menarik untuk audiens baru?]*
        - **Strategi Konten Kurang Beragam:** Akun cenderung terlalu bergantung pada format 'Posts' ([%]% dari engagement), sementara format lain seperti 'Reels' dan 'Stories' yang berpotensi meningkatkan jangkauan belum dimanfaatkan secara maksimal. *[Tunjukkan dari gambar bagaimana variasi format konten di akun pembanding berkorelasi dengan metrik jangkauan (Accounts Reached) yang lebih tinggi.]*
        - **Kesenjangan Performa Antar Akun:** Terdapat perbedaan performa yang signifikan antara akun-akun yang dianalisis, menunjukkan perlunya standarisasi strategi atau penentuan fokus prioritas. *[Gunakan gambar untuk menunjuk gaya visual mana yang paling efektif dan harus dijadikan acuan.]*

        **Solution:**
        - **Adopsi & Adaptasi Strategi Unggulan:** Analisis dan adopsi elemen sukses dari akun dengan performa terbaik, terutama strategi mereka dalam menggunakan variasi format konten untuk menarik non-followers.
        - **Diversifikasi Format Konten:** Alihkan fokus dari 'Posts' ke format 'Reels' dan 'Stories' yang lebih dinamis. Targetkan [angka] Reels dan [angka] konten TikTok/interaktif lainnya per minggu. *[Berdasarkan analisis gambar, sarankan 2-3 pilar konten visual yang bisa dieksekusi untuk memenuhi target ini.]*
        - **Tetapkan 'Standar Emas' Visual:** Jadikan performa dan kualitas visual dari akun terbaik sebagai benchmark yang harus dicapai oleh akun lain yang menjadi prioritas. *[Pilih satu gambar yang paling efektif dari yang dianalisis untuk dijadikan contoh 'standar emas' ini.]*

        **Implementation:**
        - **Buat 'Playbook' Konten Sukses:** Bedah postingan teratas dari akun terbaik. Analisis format, caption, hashtag, dan audionya untuk dibuatkan panduan praktis yang bisa ditiru.
        - **Rancang Jadwal Editorial Baru:** Buat jadwal konten mingguan yang memprioritaskan format-format yang kurang dimanfaatkan (misal: Reels dan Stories Interaktif) untuk secara aktif mengejar engagement dan jangkauan.
        - **Tetapkan KPI Jangkauan Non-Follower:** Targetkan akun dengan performa rendah untuk mencapai minimal [%]% engagement dari non-followers dalam [angka] hari ke depan sebagai metrik keberhasilan utama.
        - **Uji Coba A/B Testing untuk Format Baru:** Buat dua jenis konten visual yang berbeda di minggu pertama (misal: Reels sinematik vs. Reels 'raw'/otentik). Ukur mana yang mendapatkan jangkauan non-follower lebih baik. *[Sarankan ide spesifik untuk kedua format ini.]*

        ---
        Pastikan output akhir Anda hanya berisi laporan yang sudah terisi lengkap (dengan angka dan analisis) sesuai format di atas, menggunakan markdown.
        ";
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedData['gambar'] = $filename;
            }

            ItMultimediaTiktok::create($validatedData);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ItMultimediaTiktok $tiktok)
    {
        try {

            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedData['gambar'] = $filename;
            }

            $tiktok->update($validatedData);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(ItMultimediaTiktok $tiktok)
    {
        try {
            $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $tiktok->delete();

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $laporans = ItMultimediaTiktok::where('tanggal', $validatedData['tanggal'])->get();
    
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
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O');
    
            // Tambahkan footer
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Tiktok Multimedia Report|');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/it/multimediatiktok/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/it/multimediatiktok/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>File not found</p>";
                }
    
                // Konten untuk setiap laporan
                $htmlContent = "
            <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                {$imageHTML}
                    <h3 style='margin: 0; padding: 0;'>Description : {$laporan->keterangan}</h3>
                    <h3 style='margin: 0; padding: 0;'>Report : {$laporan->tanggal_formatted}</h3>
            </div>

                ";
    
                // Tambahkan ke PDF
                $mpdf->WriteHTML($htmlContent);
            }
            // Output PDF
            return response($mpdf->Output("laporan_multimedia_tiktok_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_multimedia_tiktok.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
