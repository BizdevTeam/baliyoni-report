<?php

namespace App\Http\Controllers;

use App\Models\LaporanRasio;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class LaporanRasioController extends Controller
{
    use DateValidationTraitAccSPI;
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanRasio::query();
    
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
        $laporanrasios = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->paginate($perPage);

        // Ubah path gambar agar dapat diakses dari frontend
        $laporanrasios->getCollection()->transform(function ($item) {
            $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/accounting/rasio/{$item->gambar}"))
                ? asset("images/accounting/rasio/{$item->gambar}")
                : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
                return $item;
            });
        $aiInsight = null;

        if (!$request->ajax() && $request->has('generate_ai')) {
            // ambil collection (halaman saat ini)
            $postsToAnalyze = $laporanrasios->getCollection();
            // panggil fungsi analisis batch gambar
            $aiInsight = $this->generateImageBatchAnalysis($postsToAnalyze);
        }
        
            if ($request->ajax()) {
                return response()->json(['laporanrasios' => $laporanrasios]);
            }

        return view('accounting.rasio', compact('laporanrasios','aiInsight'));
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
            return 'Tidak ada laporan rasio untuk dianalisis.';
        }

        // 3. Kumpulkan inline_data semua gambar rasio
        $imageParts = [];
        foreach ($rows as $item) {
            if (empty($item->gambar)) continue;
            $path = public_path("images/accounting/rasio/{$item->gambar}");
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
            return 'Tidak ada file gambar rasio yang valid untuk dianalisis.';
        }

        // 4. Buat prompt khusus rasio
        $count       = count($imageParts);
        $textPrompt  = $this->createFormattedRasioPrompt($count);

        // 5. Gabungkan teks + gambar
        $payloadParts = array_merge(
            [['text' => $textPrompt]],
            $imageParts
        );

        // 6. Kirim ke Gemini Vision API
        try {
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl . '?key=' . $apiKey, [
                    'contents' => [['parts' => $payloadParts]],
                    'generationConfig' => [
                        'temperature'     => 0.4,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari gambar rasio.';
            }
            Log::error('Gemini Vision API error: ' . $response->body());
            return 'Gagal menghubungi layanan AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan saat menghasilkan analisis rasio.';
        }
    }

    private function createFormattedRasioPrompt(int $imageCount): string
    {
        return <<<PROMPT
            Anda adalah Analis Keuangan senior yang ahli dalam analisis likuiditas dan struktur modal.  
            Saya telah mengirim **{$imageCount} screenshot** tabel Rasio keuangan untuk dua periode (JAN dan FEB), dengan kolom:

            - **Current Ratio** (Aktiva Lancar ÷ Hutang Lancar)  
            - **Cash Ratio** (Kas + Setara Kas ÷ Hutang Lancar)  
            - **Debt Ratio** (Total Hutang ÷ Total Aset)  
            - dan **Growth %** antara FEB vs JAN.  

            **TUGAS ANDA:**  
            1. **Ekstrak & Bandingkan Nilai**  
            - Tuliskan nilai Current, Cash, dan Debt Ratio untuk JAN & FEB.  
            - Sertakan perubahan persen (Growth %) yang terlihat di tabel.  
            2. **Interpretasi Tren**  
            - Apakah likuiditas membaik atau menurun? Jelaskan untuk Current & Cash Ratio.  
            - Apakah leverage (Debt Ratio) stabil atau meningkat?  
            3. **Dampak & Risiko**  
            - Jelaskan implikasi angka-angka tersebut terhadap kemampuan perusahaan membayar kewajiban jangka pendek dan struktur modal.  
            4. **Rekomendasi**  
            - Berikan 2–3 saran konkret untuk memperbaiki rasio likuiditas atau mengelola utang.  
            5. **Langkah Selanjutnya**  
            - Sarankan milestone untuk 1–2 bulan ke depan (misalnya target rasio, kebijakan kas, refinancing).  

            **FORMAT OUTPUT (Markdown):**  
            - Ringkasan Eksekutif (1 paragraf)  
            - Tabel Ringkas Nilai & Growth (%)  
            - Interpretasi Tren & Risiko (poin)  
            - Rekomendasi & Milestone (poin)  

            Gunakan bahasa Indonesia formal dan profesional.
            PROMPT;
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string|max:255'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            if ($request->hasFile('file_excel')) {
                $filename = time() . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/rasio'), $filename);
                $validatedData['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/rasio'), $excelfilename);
                $validatedData['gambar'] = $excelfilename;
            }
    
            LaporanRasio::create($validatedData);
    
            return redirect()->route('rasio.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanRasio $rasio)
    {
        try {
            $fileRules = $rasio->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'mimes:xlsx,xls|max:2048';
            $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
            'file_excel' => $fileRules,
            'keterangan' => 'required|string|max:255'
        ]);
        $errorMessage = '';
        if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
            return redirect()->back()->with('error', $errorMessage);
        }

        if ($request->hasFile('gambar')) {
            $destinationimages = "images/accounting/rasio/" . $rasio->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }

            $filename = time() . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('images/accounting/rasio'), $filename);
            $validatedData['gambar'] = $filename;
        }

        if ($request->hasFile('file_excel')) {
            $destinationfiles = "files/accounting/rasio/" . $rasio->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

            $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
            $request->file('file_excel')->move(public_path('files/accounting/rasio'), $excelfilename);
            $validatedData['file_excel'] = $excelfilename;
        }

        $rasio->update($validatedData);

        return redirect()->route('rasio.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanRasio $rasio)
    {
        try {
            $destinationimages = "images/accounting/rasio/" . $rasio->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/rasio/" . $rasio->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $rasio->delete();
        
        return redirect()->route('rasio.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
        $laporans = LaporanRasio::where('tanggal', $validatedData['tanggal'])->get();
            
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

        // Tambahkan gambar sebagai header tanpa margin
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Financial Ratio Report|');

        // Loop melalui setiap laporan dan tambahkan ke PDF
        foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->gambar) && file_exists(public_path("images/accounting/rasio/{$laporan->gambar}"))) {
                $imagePath = public_path("images/accounting/rasio/{$laporan->gambar}");
                $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Thumbnail not founf</p>";
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
            return response($mpdf->Output("Laporan_Rasio_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Laba_Rugi.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    
}
