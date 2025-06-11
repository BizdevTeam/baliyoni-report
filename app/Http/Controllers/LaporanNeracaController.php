<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNeraca;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;

class LaporanNeracaController extends Controller
{
    use DateValidationTraitAccSPI; 
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanNeraca::query();
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
        $laporanneracas = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

            // Ubah path gambar agar dapat diakses dari frontend
            $laporanneracas->getCollection()->transform(function ($item) {
                $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/accounting/neraca/{$item->gambar}"))
                    ? asset("images/accounting/neraca/{$item->gambar}")
                    : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
                return $item;
            });

        if ($request->ajax()) {
            return response()->json(['laporanneracas' => $laporanneracas]);
        }
        return view('accounting.neraca', compact('laporanneracas'));
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
