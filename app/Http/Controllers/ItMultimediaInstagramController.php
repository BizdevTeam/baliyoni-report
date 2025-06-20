<?php

namespace App\Http\Controllers;

use App\Models\ItMultimediaInstagram;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class ItMultimediaInstagramController extends Controller
{
    use DateValidationTrait;
    
    // public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');
    //     $startMonth = $request->input('start_month');
    //     $endMonth = $request->input('end_month');
    
    //     $query = ItMultimediaInstagram::query();
    
    //     // Filter berdasarkan tanggal jika ada
    //     if ($search) {
    //         $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
    //     }
    
    //     // Filter berdasarkan range bulan-tahun jika keduanya diisi
    //     if (!empty($startMonth) && !empty($endMonth)) {
    //         try {
    //             $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
    //             $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
    //             $query->whereBetween('tanggal', [$startDate, $endDate]);
    //         } catch (Exception $e) {
    //             return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
    //         }
    //     }
    //     // Ambil data dengan pagination
    //     $itmultimediainstagrams = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
    //                               ->paginate($perPage);
            
    //           // Ubah path gambar agar dapat diakses dari frontend
    //           $itmultimediainstagrams->getCollection()->transform(function ($item) {
    //             $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/it/multimediainstagram/{$item->gambar}"))
    //                 ? asset("images/it/multimediainstagram/{$item->gambar}")
    //                 : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
    //             return $item;
    //         });
        
    //         if ($request->ajax()) {
    //             return response()->json(['itmultimediainstagrams' => $itmultimediainstagrams]);
    //         }

    //     return view('it.multimediainstagram', compact('itmultimediainstagrams'));
    // }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = ItMultimediaInstagram::query();
    
        // Filter berdasarkan tanggal jika ada (sudah benar)
        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }
    
        // Filter berdasarkan range bulan-tahun jika keduanya diisi (sudah benar)
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Sebaiknya kembalikan ke view dengan pesan error, bukan JSON, kecuali ini API endpoint
                return back()->withErrors(['msg' => 'Format tanggal tidak valid. Gunakan format Y-m.']);
            }
        }

        // Ambil data dengan pagination
        $itmultimediainstagrams = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                        ->paginate($perPage);
            
        // Ubah path gambar agar dapat diakses dari frontend (sudah benar)
        $itmultimediainstagrams->getCollection()->transform(function ($item) {
            $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/it/multimediainstagram/{$item->gambar}"))
                ? asset("images/it/multimediainstagram/{$item->gambar}")
                : asset("images/no-image.png");
    
            return $item;
        });

        // --- PERUBAHAN UTAMA DIMULAI DI SINI ---

        $aiInsight = null; // Inisialisasi variabel
        
        // Hanya jalankan analisis AI untuk request non-AJAX untuk efisiensi
        if (!$request->ajax()) {
             // Ambil koleksi data yang sudah dipaginasi
            $postsToAnalyze = $itmultimediainstagrams->getCollection();
            // Panggil fungsi analisis gambar yang baru
            $aiInsight = $this->generateImageBatchAnalysis($postsToAnalyze);
        }
        
        if ($request->ajax()) {
            return response()->json(['itmultimediainstagrams' => $itmultimediainstagrams]);
        }

        // Tambahkan $aiInsight ke data yang dikirim ke view
        return view('it.multimediainstagram', compact('itmultimediainstagrams', 'aiInsight'));
    }

      private function generateImageBatchAnalysis($instagramPosts)
    {
        // 1. Setup Kredensial API
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) {
            Log::error('Gemini API Key or URL is not configured.');
            return 'Layanan AI tidak terkonfigurasi dengan benar.';
        }

        if ($instagramPosts->isEmpty()) {
            return 'Tidak ada postingan untuk dianalisis.';
        }

        try {
            // 2. Kumpulkan semua gambar yang valid
            $imageParts = [];
            foreach ($instagramPosts as $post) {
                // Lewati jika nama file gambar kosong
                if (empty($post->gambar)) {
                    continue;
                }

                $imagePath = public_path("images/it/multimediainstagram/{$post->gambar}");

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


    // private function generateImageBatchAnalysis($instagramPosts)
    // {
    //     $apiKey = config('services.gemini.api_key');
    //     $apiUrl = config('services.gemini.api_url');

    //     if (!$apiKey || !$apiUrl) {
    //         Log::error('Gemini API Key or URL is not configured.');
    //         return 'Layanan AI tidak terkonfigurasi dengan benar.';
    //     }

    //     if ($instagramPosts->isEmpty()) {
    //         return 'Tidak ada postingan untuk dianalisis.';
    //     }

    //     try {
    //         // 1. Siapkan prompt teks
    //         $textPrompt = $this->createImageBatchAnalysisPrompt();
    //         // $textPrompt = $this->createSocialStrategyPrompt();

    //         // 2. Siapkan data gambar
    //         $imageParts = [];
    //         foreach ($instagramPosts as $post) {
    //             // Dapatkan path lengkap ke file gambar di server
    //             $imagePath = public_path("images/it/multimediainstagram/{$post->gambar}");

    //             // Pastikan file ada dan bisa dibaca sebelum diproses
    //             if (!empty($post->gambar) && file_exists($imagePath) && is_readable($imagePath)) {
    //                 $imageParts[] = [
    //                     'inline_data' => [
    //                         'mime_type' => mime_content_type($imagePath),
    //                         'data'      => base64_encode(file_get_contents($imagePath))
    //                     ]
    //                 ];
    //             }
    //         }

    //         if (empty($imageParts)) {
    //             return 'Tidak ada file gambar yang valid untuk dianalisis.';
    //         }

    //         // 3. Gabungkan prompt teks dengan semua gambar
    //         $payloadContents = array_merge(
    //             [['text' => $textPrompt]],
    //             $imageParts
    //         );

    //         // 4. Kirim request ke Gemini API
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //         ])->post($apiUrl . '?key=' . $apiKey, [
    //             'contents' => [
    //                 [
    //                     'parts' => $payloadContents
    //                 ]
    //             ],
    //             'generationConfig' => [
    //                 'temperature' => 0.5,
    //                 'maxOutputTokens' => 2048,
    //             ]
    //         ]);

    //         if ($response->successful()) {
    //             $result = $response->json();
    //             return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari gambar.';
    //         } else {
    //             Log::error('Gemini Vision API error: ' . $response->body());
    //             return 'Gagal menghubungi layanan analisis gambar AI. Cek log untuk detail.';
    //         }

    //     } catch (\Exception $e) {
    //         Log::error('Error generating image AI insight: ' . $e->getMessage());
    //         return 'Terjadi kesalahan dalam menghasilkan analisis gambar.';
    //     }
    // }

    /**
     * Membuat prompt untuk menganalisis sekumpulan gambar Instagram.
     *
     * @return string
     */
    // private function createImageBatchAnalysisPrompt()
    // {
    //     // return "Anda adalah seorang spesialis media sosial yang ahli dalam menganalisis konten visual.
        
    //     // Di bawah ini saya berikan sekumpulan gambar dari postingan Instagram. Tugas Anda adalah:
    //     // 1.  **Analisis Keseluruhan:** Lihat semua gambar secara keseluruhan dan identifikasi tema visual atau pola umum yang muncul (misalnya: apakah temanya tentang event, promosi, atau edukasi? Apa palet warna yang dominan?).
    //     // 2.  **Analisis Individual (Singkat):** Untuk setiap gambar, berikan deskripsi singkat (1-2 kalimat) tentang apa yang ditampilkan dan apa pesan yang ingin disampaikan.
    //     // 3.  **Rekomendasi Konten:** Berdasarkan analisis Anda, berikan 2-3 ide atau rekomendasi untuk konten selanjutnya agar lebih menarik dan efektif.

    //     // Sajikan jawaban Anda dalam format markdown yang terstruktur dengan baik agar mudah dibaca.";
    //     return "Anda adalah seorang ahli strategi media sosial senior yang sangat teliti dan berorientasi pada data.

    //     **TUGAS ANDA:**
    //     Saya akan memberikan sekumpulan gambar dari postingan Instagram. Analisis semua gambar tersebut secara mendalam. Kemudian, gunakan hasil analisis visual Anda untuk **mengisi dan melengkapi** sebuah laporan strategis dengan format **TEPAT** seperti template di bawah ini.

    //     Gunakan wawasan dari gambar untuk memberikan detail, bukti, dan contoh pada setiap poin di dalam laporan. Jangan hanya mengulang poin yang sudah ada, tetapi kembangkan dengan temuan visual Anda.
    //     Gunakan gambar yang ada untuk memberikan analisis kinerja dan rencana tindak lanjut yang spesifik dan actionable, sesuai template laporan yang sudah disediakan.
    //     Analisislah gambar yang ada, ketika sudah mendapatkan insight, gunakan untuk mengisi template laporan yang sudah disediakan di bawah ini.
    //     ---
    //     **TEMPLATE LAPORAN (Isi bagian ini berdasarkan analisis gambar):**

    //     ### Analisis Kinerja & Rencana Tindak Lanjut

    //     **Problem:**
    //     - Views Tiktok naik signifikan, namun komentar turun (-27,27%). *[Dari analisis gambar, jelaskan mengapa konten visual yang ada saat ini mungkin gagal memancing komentar. Apakah terlalu pasif? Kurang ada 'call to discussion'? Berikan contoh dari salah satu gambar.]*
    //     - Engagement masih didominasi oleh followers, non-followers belum aktif terutama di @balimall.id. *[Dari analisis gambar, identifikasi apakah gaya visual saat ini terlalu 'niche' atau kurang memiliki 'wow-factor' untuk dibagikan oleh audiens baru.]*
    //     - Terdapat 13 postingan di bulan Februari, yang masih di bawah target untuk menjaga engagement stabil. *[Dari analisis gambar, apakah ada indikasi bahwa proses pembuatan konten saat ini rumit sehingga sulit untuk diproduksi secara massal?]*
    //     - Pada Januari, ada rencana filtering akun sosmed, tetapi Februari belum ada laporan pembaruan terkait akun prioritas. *[Dari analisis gambar, tunjukkan gaya visual dari postingan mana yang paling kuat dan mana yang paling lemah untuk membantu proses filtering.]*

    //     **Solution:**
    //     - Membuat laporan dan analisa terkait update algoritma tiktok dan instagram untuk tahun 2025, sehingga bisa menyesuaikan strategi terbaru untuk meningkatkan Engagement.
    //     - Tingkatkan jumlah postingan mingguan: - Minimal 3-4 Reels per minggu. - 1-2 konten TikTok per minggu. *[Berdasarkan analisis gambar, sarankan 2-3 tema atau pilar konten visual yang bisa dieksekusi untuk memenuhi target ini, contoh: pilar edukasi, pilar hiburan, pilar testimoni.]*
    //     - Pastikan akun yang akan difokuskan sudah ditetapkan. *[Gunakan gambar yang paling efektif sebagai contoh untuk 'standar emas' visual yang harus diikuti oleh akun prioritas.]*

    //     **Implementation:**
    //     - Mempersiapkan laporan analisa algoritma tiktok dan instagram untuk tahun 2025.
    //     - Uji beberapa variasi Reels dengan format berbeda untuk melihat respons audiens. *[Berikan 3 contoh format Reels spesifik berdasarkan analisis gambar, contoh: 'Format A: Video close-up produk dengan teks naratif', 'Format B: Kompilasi foto event dengan musik tren', dll.]*
    //     - Buat jadwal editorial untuk memastikan konsistensi posting untuk menambah engagement harian.
    //     - Buat analisis performa setiap akun untuk melihat efektivitasnya. Jika merger diperlukan, siapkan transisi konten agar tidak kehilangan audiens. *[Sarankan bagaimana gaya visual dari akun yang akan dimerger bisa digabungkan secara harmonis berdasarkan contoh gambar yang ada.]*

    //     ---
    //     Pastikan output akhir Anda hanya berisi laporan yang sudah terisi lengkap sesuai format di atas, menggunakan markdown untuk heading, bold, dan list.
    //     ";
    // }

   
    
    /**
     * Prompt yang diperbaiki untuk menerima jumlah gambar secara dinamis,
     * membuat instruksi ke AI menjadi lebih jelas dan tegas.
     *
     * @param int $imageCount Jumlah gambar yang akan dianalisis
     * @return string
     */
    // private function createFormattedReportPrompt(int $imageCount)
    // {
    //     return "Anda adalah seorang ahli strategi media sosial senior yang sangat teliti dan berorientasi pada data.

    //     **TUGAS UTAMA ANDA:**
    //     Saya telah memberikan Anda **{$imageCount} gambar** dari postingan Instagram. Analisis **SEMUA GAMBAR INI** secara spesifik dan mendalam. Setelah itu, gunakan hasil analisis visual Anda untuk **mengisi dan melengkapi** sebuah laporan strategis dengan format **TEPAT** seperti template di bawah ini.

    //     Setiap poin yang Anda tulis dalam laporan HARUS didasarkan pada BUKTI VISUAL dari gambar-gambar yang diberikan. Jangan memberikan jawaban generik.

    //     ---
    //     **TEMPLATE LAPORAN (Isi bagian ini berdasarkan analisis {$imageCount} gambar yang diberikan):**

    //     ### Analisis Kinerja & Rencana Tindak Lanjut

    //     ### Analisis Kinerja & Rencana Tindak Lanjut (Periode 26 Ags - 25 Sep)

    //     **Problem:**
    //     - **Kualitas Engagement Rendah di @balimall.id:** Meskipun memiliki 636 Views, interaksi yang dihasilkan sangat rendah (hanya 79 interaksi dari 18 akun). Ini menunjukkan konten gagal mengubah penonton menjadi partisipan aktif. *[Dari analisis gambar, jelaskan mengapa konten visual @balimall.id yang 100% format 'Posts' ini bersifat pasif dan tidak memancing komentar.]*
    //     - **Jangkauan Non-Followers Nihil (0%) untuk @balimall.id:** Akun ini gagal total menjangkau audiens di luar pengikutnya (0% engagement dari non-followers). Ini sangat kontras dengan `@baliyonigroup` yang berhasil meraih 43.6% audiens baru. *[Lihat gaya visual @balimall.id. Apakah terlalu 'standar' atau kurang memiliki 'wow-factor' sehingga tidak menarik untuk dibagikan oleh audiens baru? Bandingkan dengan variasi konten di @baliyonigroup.]*
    //     - **Strategi Konten @balimall.id Tidak Beragam:** Akun ini terlalu bergantung pada format 'Posts' (92.9% impresi dan 100% engagement dari format ini), sementara format 'Reels' dan 'Stories' yang berpotensi viral tidak dimanfaatkan secara efektif. *[Dari analisis gambar, tunjukkan bagaimana diversifikasi konten di @baliyonigroup (Posts, Stories, Reels) berkorelasi dengan jangkauan akun yang lebih tinggi (404 reached vs 121 reached).] *
    //     - **Kesenjangan Performa Antar Akun Sangat Jelas:** Terdapat perbedaan performa yang drastis antara `@baliyonigroup` (jangkauan luas, audiens baru, konten beragam) dan `@balimall.id` (terisolasi, tidak ada audiens baru, konten monoton). *[Gunakan data perbandingan di gambar untuk menjustifikasi mengapa strategi @baliyonigroup harus menjadi model acuan.]*

    //     **Solution:**
    //     - **Adopsi & Adaptasi Strategi @baliyonigroup:** Segera analisis dan adopsi elemen-elemen sukses dari `@baliyonigroup`, terutama strategi mereka dalam menggunakan variasi format konten untuk menarik non-followers.
    //     - **Diversifikasi Format Konten di @balimall.id:** Alihkan fokus dari 'Posts' ke format 'Reels' dan 'Stories'. Targetkan minimal 3-4 Reels per minggu untuk meningkatkan potensi jangkauan viral, meniru keberhasilan jangkauan non-follower `@baliyonigroup`. *[Dari analisis gambar, sarankan 2 pilar konten yang bisa diangkat menjadi Reels, misalnya pilar 'Event Highlight' dan pilar 'Product Feature'.]*
    //     - **Jadikan @baliyonigroup sebagai 'Standar Emas':** Tetapkan metrik dan kualitas visual dari `@baliyonigroup` sebagai benchmark performa yang harus dicapai oleh `@balimall.id`. *[Gunakan gambar @baliyonigroup yang paling mungkin efektif sebagai contoh 'standar emas' visual yang harus diikuti.]*

    //     **Implementation:**
    //     - **Buat 'Playbook' Konten Sukses:** Bedah 3 postingan teratas dari `@baliyonigroup` pada periode ini. Analisis format, caption, hashtag, dan audionya untuk dibuatkan panduan praktis yang bisa langsung ditiru oleh tim `@balimall.id`.
    //     - **Rancang Jadwal Editorial Baru untuk @balimall.id:** Buat jadwal konten mingguan yang memprioritaskan Reels (misal: Senin, Rabu, Jumat) dan Stories interaktif (misal: Polling, Q&A setiap Selasa/Kamis) untuk secara aktif mengejar engagement.
    //     - **Tetapkan KPI Jangkauan Non-Follower:** Targetkan `@balimall.id` untuk mencapai minimal **15% engagement dari non-followers** dalam 30 hari ke depan sebagai metrik keberhasilan utama (Objective and Key Result - OKR).
    //     - **Uji Coba A/B Testing untuk Reels:** Buat dua jenis Reels di minggu pertama: satu dengan gaya sinematik seperti `@baliyonigroup` dan satu lagi dengan gaya yang lebih 'raw' dan otentik. Ukur mana yang mendapatkan jangkauan non-follower lebih baik. *[Sarankan ide spesifik untuk kedua format Reels ini berdasarkan kemungkinan jenis usaha dari brand.]*

    //     ---
    //     Pastikan output akhir Anda hanya berisi laporan yang sudah terisi lengkap sesuai format di atas, menggunakan markdown untuk heading, bold, dan list.
    //     ";
    // }

   

    //  private function createSocialStrategyPrompt()
    // {
    //     // Mendapatkan bulan dan tahun saat ini untuk konteks
    //     $currentDate = \Carbon\Carbon::now();
    //     $currentMonth = $currentDate->translatedFormat('F'); // Contoh: Juni
    //     $currentYear = $currentDate->year; // Contoh: 2025
 
    //     return "Anda adalah seorang ahli strategi media sosial senior yang sangat teliti dan berorientasi pada data.

    //     **TUGAS ANDA:**
    //     Saya akan memberikan sekumpulan gambar dari postingan Instagram. Analisis semua gambar tersebut secara mendalam. Kemudian, gunakan hasil analisis visual Anda untuk **mengisi dan melengkapi** sebuah laporan strategis dengan format **TEPAT** seperti template di bawah ini.

    //     Gunakan wawasan dari gambar untuk memberikan detail, bukti, dan contoh pada setiap poin di dalam laporan. Jangan hanya mengulang poin yang sudah ada, tetapi kembangkan dengan temuan visual Anda.

    //     ---
    //     **TEMPLATE LAPORAN (Isi bagian ini berdasarkan analisis gambar):**

    //     ### Analisis Kinerja & Rencana Tindak Lanjut

    //     **Problem:**
    //     - Views Tiktok naik signifikan, namun komentar turun (-27,27%). *[Dari analisis gambar, jelaskan mengapa konten visual yang ada saat ini mungkin gagal memancing komentar. Apakah terlalu pasif? Kurang ada 'call to discussion'?]*
    //     - Engagement masih didominasi oleh followers, non-followers belum aktif terutama di @balimall.id. *[Dari analisis gambar, identifikasi apakah gaya visual saat ini terlalu 'niche' atau kurang memiliki 'wow-factor' untuk dibagikan oleh audiens baru.]*
    //     - Terdapat 13 postingan di bulan Februari, yang masih di bawah target untuk menjaga engagement stabil. *[Dari analisis gambar, apakah ada indikasi bahwa proses pembuatan konten saat ini rumit sehingga sulit untuk diproduksi secara massal?]*
    //     - Pada Januari, ada rencana filtering akun sosmed, tetapi Februari belum ada laporan pembaruan terkait akun prioritas. *[Dari analisis gambar, tunjukkan gaya visual dari postingan mana yang paling kuat dan mana yang paling lemah untuk membantu proses filtering.]*

    //     **Solution:**
    //     - Membuat laporan dan analisa terkait update algoritma tiktok dan instagram untuk tahun 2025, sehingga bisa menyesuaikan strategi terbaru untuk meningkatkan Engagement.
    //     - Tingkatkan jumlah postingan mingguan: - Minimal 3-4 Reels per minggu. - 1-2 konten TikTok per minggu. *[Berdasarkan analisis gambar, sarankan 2-3 tema atau pilar konten visual yang bisa dieksekusi untuk memenuhi target ini, contoh: pilar edukasi, pilar hiburan, pilar testimoni.]*
    //     - Pastikan akun yang akan difokuskan sudah ditetapkan. *[Gunakan gambar yang paling efektif sebagai contoh untuk 'standar emas' visual yang harus diikuti oleh akun prioritas.]*

    //     **Implementation:**
    //     - Mempersiapkan laporan analisa algoritma tiktok dan instagram untuk tahun 2025.
    //     - Uji beberapa variasi Reels dengan format berbeda untuk melihat respons audiens. *[Berikan 3 contoh format Reels spesifik berdasarkan analisis gambar, contoh: 'Format A: Video close-up produk dengan teks naratif', 'Format B: Kompilasi foto event dengan musik tren', dll.]*
    //     - Buat jadwal editorial untuk memastikan konsistensi posting untuk menambah engagement harian.
    //     - Buat analisis performa setiap akun untuk melihat efektivitasnya. Jika merger diperlukan, siapkan transisi konten agar tidak kehilangan audiens. *[Sarankan bagaimana gaya visual dari akun yang akan dimerger bisa digabungkan secara harmonis berdasarkan contoh gambar yang ada.]*

    //     ---
    //     Pastikan output akhir Anda hanya berisi laporan yang sudah terisi lengkap sesuai format di atas, menggunakan markdown untuk heading, bold, dan list.
    //     ";
    // }

    /**
     * Menampilkan halaman utama galeri Instagram dan hasil analisis AI.
     */
    // public function index(Request $request)
    // {
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');
    //     $startMonth = $request->input('start_month');
    //     $endMonth = $request->input('end_month');
    
    //     $query = ItMultimediaInstagram::query();
    
    //     if ($search) {
    //         $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
    //     }
    
    //     if (!empty($startMonth) && !empty($endMonth)) {
    //         try {
    //             $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
    //             $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
    //             $query->whereBetween('tanggal', [$startDate, $endDate]);
    //         } catch (\Exception $e) {
    //             return back()->withErrors(['msg' => 'Format tanggal tidak valid. Gunakan format Y-m.']);
    //         }
    //     }

    //     $itmultimediainstagrams = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')->paginate($perPage);
            
    //     $itmultimediainstagrams->getCollection()->transform(function ($item) {
    //         $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/it/multimediainstagram/{$item->gambar}"))
    //             ? asset("images/it/multimediainstagram/{$item->gambar}")
    //             : asset("images/no-image.png");
    //         return $item;
    //     });

    //     $aiInsight = null;
        
    //     if (!$request->ajax()) {
    //         $postsToAnalyze = $itmultimediainstagrams->getCollection();
    //         $aiInsight = $this->generateImageAnalysisReport($postsToAnalyze);
    //     }
        
    //     if ($request->ajax()) {
    //         return response()->json(['itmultimediainstagrams' => $itmultimediainstagrams]);
    //     }

    //     return view('it.multimediainstagram', compact('itmultimediainstagrams', 'aiInsight'));
    // }

    // private function generateImageAnalysisReport($instagramPosts)
    // {
    //     $apiKey = config('services.gemini.api_key');
    //     $apiUrl = config('services.gemini.api_url');

    //     if (!$apiKey || !$apiUrl) {
    //         Log::error('Gemini API Key/URL tidak dikonfigurasi.');
    //         return 'Layanan AI tidak terkonfigurasi dengan benar.';
    //     }

    //     if ($instagramPosts->isEmpty()) {
    //         return 'Tidak ada postingan untuk dianalisis.';
    //     }

    //     try {
    //         $imageParts = [];
    //         foreach ($instagramPosts as $post) {
    //             if (empty($post->gambar)) continue;

    //             $imagePath = public_path("images/it/multimediainstagram/{$post->gambar}");

    //             if (file_exists($imagePath) && is_readable($imagePath)) {
    //                 $imageParts[] = [
    //                     'inline_data' => [
    //                         'mime_type' => mime_content_type($imagePath),
    //                         'data'      => base64_encode(file_get_contents($imagePath))
    //                     ]
    //                 ];
    //             }
    //         }

    //         if (empty($imageParts)) {
    //             return 'Tidak ada file gambar valid untuk dianalisis pada rentang data ini.';
    //         }

    //         // Panggil fungsi untuk membuat prompt SEKARANG, setelah tahu jumlah gambar
    //         $textPrompt = $this->createFormattedReportPrompt(count($imageParts));
            
    //         $payloadContents = array_merge([['text' => $textPrompt]], $imageParts);

    //         $response = Http::timeout(120)->post($apiUrl . '?key=' . $apiKey, [
    //             'contents' => [['parts' => $payloadContents]],
    //             'generationConfig' => [
    //                 'temperature'     => 0.4,
    //                 'maxOutputTokens' => 4096,
    //             ]
    //         ]);

    //         if ($response->successful()) {
    //             $result = $response->json();
    //             return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan insight dari gambar.';
    //         } else {
    //             Log::error('Gemini Vision API error: ' . $response->body());
    //             return 'Gagal menghubungi layanan analisis AI. Isi respons error telah dicatat di log.';
    //         }

    //     } catch (\Exception $e) {
    //         Log::error('Error generating image AI insight: ' . $e->getMessage());
    //         return 'Terjadi kesalahan sistem saat menghasilkan analisis gambar.';
    //     }
    // }
    
    // /**
    //  * Versi perbaikan dari prompt, lebih eksplisit dalam instruksinya.
    //  */
    // private function createFormattedReportPrompt(int $imageCount)
    // {
    //     // Instruksi yang lebih eksplisit untuk AI
    //     return "Anda adalah seorang ahli strategi media sosial senior yang sangat teliti dan berorientasi pada data.

    //     **TUGAS UTAMA ANDA:**
    //     Saya telah memberikan Anda **{$imageCount} gambar** dari postingan Instagram. Analisis **SEMUA GAMBAR INI** secara spesifik dan mendalam. Setelah itu, gunakan hasil analisis visual Anda untuk **mengisi dan melengkapi** sebuah laporan strategis dengan format **TEPAT** seperti template di bawah ini.

    //     Setiap poin yang Anda tulis dalam laporan HARUS didasarkan pada BUKTI VISUAL dari gambar-gambar yang diberikan. Jangan memberikan jawaban generik.

    //     ---
    //     **TEMPLATE LAPORAN (Isi bagian ini berdasarkan analisis {$imageCount} gambar yang diberikan):**

    //     ### Analisis Kinerja & Rencana Tindak Lanjut

    //     **Problem:**
    //     - Views Tiktok naik signifikan, namun komentar turun (-27,27%). *[Berdasarkan {$imageCount} gambar yang ada, jelaskan mengapa konten visual ini mungkin gagal memancing komentar. Apakah terlalu pasif? Kurang ada pertanyaan atau elemen interaktif dalam visual? Berikan contoh dari salah satu gambar.]*
    //     - Engagement masih didominasi oleh followers, non-followers belum aktif terutama di @balimall.id. *[Lihat {$imageCount} gambar ini. Apakah gaya visualnya punya potensi viral atau terlalu umum sehingga non-followers tidak tertarik? Identifikasi visual yang paling 'shareable' dan yang paling tidak.]*
    //     - Terdapat 13 postingan di bulan Februari, yang masih di bawah target untuk menjaga engagement stabil. *[Apakah gaya visual dari {$imageCount} gambar ini terlihat rumit dan memakan waktu untuk dibuat? Berikan penilaian tingkat kesulitan produksi visualnya.]*
    //     - Pada Januari, ada rencana filtering akun sosmed, tetapi Februari belum ada laporan pembaruan terkait akun prioritas. *[Pilih satu gambar yang visualnya paling kuat dan satu gambar yang paling lemah dari {$imageCount} gambar ini. Gunakan pilihan Anda sebagai dasar rekomendasi untuk filtering akun.]*

    //     **Solution:**
    //     - Membuat laporan dan analisa terkait update algoritma tiktok dan instagram untuk tahun 2025.
    //     - Tingkatkan jumlah postingan mingguan: - Minimal 3-4 Reels per minggu. - 1-2 konten TikTok per minggu. *[Dari {$imageCount} gambar ini, sarankan 2-3 tema atau pilar konten visual yang bisa dieksekusi, contoh: pilar 'Behind the Scene' berdasarkan gambar X, pilar 'Edukasi Produk' berdasarkan gambar Y.]*
    //     - Pastikan akun yang akan difokuskan sudah ditetapkan. *[Pilih satu gambar yang paling efektif dari {$imageCount} gambar ini untuk dijadikan 'standar emas' atau benchmark visual bagi akun prioritas.]*

    //     **Implementation:**
    //     - Mempersiapkan laporan analisa algoritma tiktok dan instagram untuk tahun 2025.
    //     - Uji beberapa variasi Reels dengan format berbeda untuk melihat respons audiens. *[Dari {$imageCount} gambar yang ada, berikan 3 ide format Reels yang visualnya terinspirasi dari gambar-gambar tersebut.]*
    //     - Buat jadwal editorial untuk memastikan konsistensi posting untuk menambah engagement harian.
    //     - Buat analisis performa setiap akun untuk melihat efektivitasnya. Jika merger diperlukan, siapkan transisi konten agar tidak kehilangan audiens. *[Sarankan bagaimana gaya visual dari gambar-gambar ini bisa digabungkan secara harmonis jika ada merger akun.]*

    //     ---
    //     Pastikan output akhir Anda hanya berisi laporan yang sudah terisi lengkap sesuai format di atas, menggunakan markdown.
    //     ";
    // }

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
                $request->file('gambar')->move(public_path('images/it/multimediainstagram'), $filename);
                $validatedData['gambar'] = $filename;
            }

            ItMultimediaInstagram::create($validatedData);

            return redirect()->route('multimediainstagram.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('multimediainstagram.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ItMultimediaInstagram $multimediainstagram)
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
                $destination = "images/it/multimediainstagram/" . $multimediainstagram->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediainstagram'), $filename);
                $validatedData['gambar'] = $filename;
            }

            $multimediainstagram->update($validatedData);

            return redirect()->route('multimediainstagram.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Instagram data: ' . $e->getMessage());
            return redirect()->route('multimediainstagram.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(ItMultimediaInstagram $multimediainstagram)
    {
        try {
            $destination = "images/it/multimediainstagram/" . $multimediainstagram->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $multimediainstagram->delete();

            return redirect()->route('multimediainstagram.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Instagram data: ' . $e->getMessage());
            return redirect()->route('multimediainstagram.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $laporans = ItMultimediaInstagram::where('tanggal', $validatedData['tanggal'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Instagram Multimedia Report|');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/it/multimediainstagram/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/it/multimediainstagram/{$laporan->gambar}");
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
            return response($mpdf->Output("laporan_multimedia_instagram_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_multimedia_instagram_.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
