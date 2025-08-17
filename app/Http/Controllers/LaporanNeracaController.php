<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNeraca;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class LaporanNeracaController extends Controller
{
    use DateValidationTraitAccSPI; 
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 12);
        $query = LaporanNeraca::query();

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
        $laporanneracas = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));
            // Ubah path gambar agar dapat diakses dari frontend
            $laporanneracas->getCollection()->transform(function ($item) {
                $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/accounting/neraca/{$item->gambar}"))
                    ? asset("images/accounting/neraca/{$item->gambar}")
                    : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
                return $item;
            });
        $aiInsight = null;

        if (!$request->ajax() && $request->has('generate_ai')) {
            // ambil collection (halaman saat ini)
            $postsToAnalyze = $laporanneracas->getCollection();
            // panggil fungsi analisis batch gambar
            $aiInsight = $this->generateImageBatchAnalysis($postsToAnalyze);
        }

        if ($request->ajax()) {
            return response()->json(['laporanneracas' => $laporanneracas]);
        }
        return view('accounting.neraca', compact('laporanneracas','aiInsight'));
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
            return 'Tidak ada laporan Neraca untuk dianalisis.';
        }

        // 3. Kumpulkan inline_data gambar
        $imageParts = [];
        foreach ($rows as $item) {
            if (empty($item->gambar)) continue;
            $path = public_path("images/accounting/neraca/{$item->gambar}");
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
            return 'Tidak ada file gambar Neraca yang valid untuk dianalisis.';
        }

        // 4. Buat prompt khusus Neraca
        $count = count($imageParts);
        $textPrompt = $this->createFormattedNeracaPrompt($count);

        // 5. Gabungkan teks + gambar → payload
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
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Tidak dapat menghasilkan insight dari gambar Neraca.';
            }

            Log::error('Gemini Vision API error: ' . $response->body());
            return 'Gagal menghubungi layanan AI. Cek log untuk detail.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan sistem saat menghasilkan analisis Neraca.';
        }
    }

    private function createFormattedNeracaPrompt(int $imageCount): string
    {
        return <<<PROMPT
            Anda adalah Analis Keuangan senior dengan spesialisasi di Financial Reporting.  
            Saya telah mengirim **{$imageCount} screenshot** tabel Neraca (Balance Sheet) yang mencakup:

            - **Aktiva Lancar** dan **Aktiva Tetap**, beserta subtotal dan total Aktiva.  
            - **Hutang Lancar** dan **Hutang Jangka Panjang**, subtotal dan total Hutang.  
            - **Modal** (ekuitas) dan total Pasiva.  
            - Kolom **Jumlah** dan **Common Size (%)** untuk masing-masing kategori.

            **TUGAS ANDA:**
            1. **Ekstrak Angka Utama**  
            - Total Aktiva Lancar, Aktiva Tetap, dan Total Aktiva.  
            - Total Hutang Lancar, Hutang Jangka Panjang, dan Total Hutang.  
            - Total Modal dan Total Pasiva.  

            2. **Analisis Struktur Neraca**  
            - Bandingkan proporsi Common Size Aktiva Lancar vs Aktiva Tetap.  
            - Bandingkan proporsi Hutang vs Modal.  

            3. **Hitung Rasio Keuangan**  
            - Current Ratio = Aktiva Lancar ÷ Hutang Lancar.  
            - Debt to Equity Ratio = Total Hutang ÷ Total Modal.  

            4. **Identifikasi Penyimpangan**  
            - Temukan akun terbesar (dengan common size tertinggi) di setiap kategori.  
            - Catat akun yang growth-nya paling signifikan (jika ada data perbandingan periode sebelumnya).  

            5. **Rekomendasi & Langkah Selanjutnya**  
            - Berikan 3–5 rekomendasi untuk memperbaiki struktur modal, likuiditas, atau efisiensi aset.  
            - Saran milestone implementasi untuk 1–2 kuartal ke depan.  

            **FORMAT OUTPUT (Markdown):**  
            - Ringkasan Eksekutif (1 paragraf)  
            - Angka Utama & Rasio (tabel/poin)  
            - Struktur & Penyimpangan (poin)  
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
                'file_excel' => '   mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string|max:255'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            if ($request->hasFile('file_excel')) {
                $filename = time() . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/neraca'), $filename);
                $validatedData['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/neraca'), $excelfilename);
                $validatedData['gambar'] = $excelfilename;
            }
            
            LaporanNeraca::create($validatedData);
    
            return redirect()->route('neraca.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanNeraca $neraca)
    {
        try {
            $fileRules = $neraca->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'mimes:xlsx,xls|max:2048';
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
                $destinationimages = "images/accounting/neraca/" . $neraca->gambar;
                if (File::exists($destinationimages)) {
                    File::delete($destinationimages);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/neraca'), $filename);
                $validatedData['gambar'] = $filename;
            }

            if ($request->hasFile('file_excel')) {
                $destinationfiles = "files/accounting/neraca/" . $neraca->file_excel;
                if (File::exists($destinationfiles)) {
                    File::delete($destinationfiles);
                }

                $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/neraca'), $excelfilename);
                $validatedData['file_excel'] = $excelfilename;
            }

            $neraca->update($validatedData);

            return redirect()->route('neraca.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanNeraca $neraca)
    {
        try {
            $destinationimages = "images/accounting/neraca/" . $neraca->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/neraca/" . $neraca->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $neraca->delete();
        
        return redirect()->route('neraca.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $laporans = LaporanNeraca::where('tanggal', $validatedData['tanggal'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Balance Sheet|');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/accounting/neraca/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/accounting/neraca/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Thumbnail not found</p>";
                }
    
                // Konten untuk setiap laporan
                $htmlContent = "
            <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                {$imageHTML}
                    <h3 style='margin: 0; padding: 0;'>Description : {$laporan->keterangan}</h3>
                    <h3 style='margin: 0; padding: 0;'>Report : {$laporan->date_formatted}</h3>
            </div>

                ";
    
                // Tambahkan ke PDF
                $mpdf->WriteHTML($htmlContent);
            }
            // Output PDF
            return response($mpdf->Output("Laporan_Neraca_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Neraca.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    public function getGambar(Request $request)
{
    try {
        $search = $request->input('search');

        $images = LaporanNeraca::select('gambar')
            ->whereNotNull('gambar')
            ->when($search, function ($query, $search) {
                return                 
                $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);

            })
            ->get()
            ->map(function ($item) {
                // Pastikan gambar tidak kosong dan benar-benar ada di direktori
                $imagePath = public_path('images/accounting/neraca/'.$item->gambar);

                if (!empty($item->gambar) && file_exists($imagePath)) {
                    return [
                        'gambar' => asset('images/accounting/neraca/'.$item->gambar) // Path yang benar
                    ];
                }

                return [
                    'gambar' => asset('images/no-image.png') // Placeholder jika gambar tidak ditemukan
                ];
            });

        return response()->json($images);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
