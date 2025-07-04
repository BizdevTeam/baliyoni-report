<?php

namespace App\Http\Controllers;

use App\Models\LaporanPpn;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class LaporanPpnController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanPpn::query();

        // Filter berdasarkan tanggal jika ada
        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }
    
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
            }
        }
        // Ambil data dengan pagination
        $laporanppns = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

              // Ubah path thumbnail agar dapat diakses dari frontend
        $laporanppns->getCollection()->transform(function ($item) {
            $item->gambar_url = !empty($item->thumbnail) && file_exists(public_path("images/accounting/ppn/{$item->thumbnail}"))
                ? asset("images/accounting/ppn/{$item->thumbnail}")
                : asset("images/no-image.png"); // Placeholder jika tidak ada thumbnail
        
                return $item;
            });

        $aiInsight = null;

        if (!$request->ajax() && $request->has('generate_ai')) {
            // ambil collection (halaman saat ini)
            $postsToAnalyze = $laporanppns->getCollection();
            // panggil fungsi analisis batch thumbnail
            $aiInsight = $this->generateImageBatchAnalysis($postsToAnalyze);
        }
        
            if ($request->ajax()) {
                return response()->json(['laporanppns' => $laporanppns]);
            }

        return view('accounting.laporanppn', compact('laporanppns','aiInsight'));
    }
    private function generateImageBatchAnalysis($rows): string
    {
        // 1. Convert paginator/collection → array
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
            return 'Tidak ada data PPN untuk dianalisis.';
        }

        // 3. Kumpulkan inline_data setiap thumbnail
        $imageParts = [];
        foreach ($rows as $item) {
            if (empty($item->thumbnail)) continue;
            $path = public_path("images/accounting/ppn/{$item->thumbnail}");
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
            return 'Tidak ada gambar PPN yang valid untuk dianalisis.';
        }

        // 4. Buat prompt spesifik PPN
        $count      = count($imageParts);
        $textPrompt = $this->createFormattedPpnPrompt($count);

        // 5. Gabungkan teks + gambar
        $payloadParts = array_merge(
            [['text' => $textPrompt]],
            $imageParts
        );

        // 6. Kirim ke Gemini Vision
        try {
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl . '?key=' . $apiKey, [
                    'contents' => [['parts' => $payloadParts]],
                    'generationConfig' => [
                        'temperature'     => 0.4,
                        'maxOutputTokens' => 2048,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari gambar PPN.';
            }

            Log::error('Gemini Vision API error: ' . $response->body());
            return 'Gagal menghubungi layanan AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan saat menghasilkan analisis gambar PPN.';
        }
    }
    private function createFormattedPpnPrompt(int $imageCount): string
    {
        return <<<PROMPT
            Anda adalah seorang Analis Keuangan senior yang ahli dalam manajemen piutang dan restitusi PPN.  
            Saya telah mengirim **{$imageCount} screenshot** tabel Laporan PPN berisi kolom:

            - **Outlet** (nama pelanggan/vendor)  
            - **KPP** (kantor pajak)  
            - **Nilai Restitusi** (nilai negatif dalam tanda kurung)  
            - **Koreksi** (adjustment, bisa positif atau negatif)  
            - **Uang Masuk** (nilai bersih setelah koreksi)  
            - **Tanggal Cair**  

            **TUGAS ANDA:**  
            1. **Ekstrak & Hitung**  
            - Total Nilai Restitusi (JAN–FEB) dan total Koreksi.  
            - Total Uang Masuk.  
            2. **Identifikasi Anomali**  
            - Outlet dengan restitusi terbesar (nilai absolut tertinggi).  
            - Koreksi terbesar (positif/negatif) dan kenapa mungkin terjadi.  
            - Pola tanggal cair (apakah clustering di akhir bulan?).  
            3. **Analisis Cash Flow**  
            - Bandingkan jumlah uang masuk vs total restitusi. Apakah ada selisih signifikan?  
            4. **Rekomendasi Proses**  
            - Berikan 3 saran untuk mempercepat proses pencairan atau meminimalkan koreksi ulang.  
            5. **Langkah Selanjutnya**  
            - Rekomendasi milestone perbaikan (misal: review KPP, SOP verifikasi restitusi) dalam 1–2 periode ke depan.  

            **FORMAT OUTPUT (Markdown):**  
            - **Ringkasan Eksekutif:** 1 paragraf  
            - **Angka Utama:** Poin-poin atau tabel ringkas  
            - **Anomali & Pola:** Poin-poin  
            - **Rekomendasi & Milestone:** Poin-poin dengan timeline  

            Gunakan bahasa Indonesia formal dan profesional.
            PROMPT;
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('file')) {
                $excelFileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $excelFileName);
                $validatedData['file'] = $excelFileName;
            }

            if ($request->hasFile('thumbnail')) {
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedData['thumbnail'] = $fileName;
            }

            LaporanPpn::create($validatedData);
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaporanPpn $laporanppn)
    {
        try{
            $validatedData = $request->validate([
                'tanggal' => 'required|string',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('thumbnail')) {
                $destinationImages = "images/accounting/ppn/" . $laporanppn->thumbnail;
                if (File::exists($destinationImages)) {
                    File::delete($destinationImages);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedData['thumbnail'] = $fileName;
            }

            if ($request->hasFile('file')) {
                $destinationFile = "files/accounting/ppn/" . $laporanppn->file;
                if (File::exists($destinationFile)) {
                    File::delete($destinationFile);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $fileName);
                $validatedData['file'] = $fileName;
            }

            $laporanppn->update($validatedData);
            
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil diubah!');

        } catch (\Exception $e) {
            Log::error('Error updating laporanppn: ' . $e->getMessage());
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanPpn $laporanppn)
    {
        try{            
            // Cek dan hapus file
            $destination = ('images/accounting/ppn/' . $laporanppn->thumbnail);
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $laporanppn->delete();
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika terjadi error, redirect dengan pesan error
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
    public function exportPDF(Request $request)
    {
        try {
            // Validasi input date
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
            ]);
    
            // Ambil data laporan berdasarkan date yang dipilih
            $laporans = LaporanPpn::where('tanggal', $validatedData['tanggal'])->get();
    
            if (!$laporans) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
    
            // Inisialisasi mPDF
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 20, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);
    
            // Tambahkan thumbnail sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - PPN Report|');
    
           // Loop melalui setiap laporan dan tambahkan ke PDF
           foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->thumbnail) && file_exists(public_path("images/accounting/ppn/{$laporan->thumbnail}"))) {
                $imagePath = public_path("images/accounting/ppn/{$laporan->thumbnail}");
                $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Thumbnail not found</p>";
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
            return response($mpdf->Output("Laporan_PPn_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_PPN.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

}
