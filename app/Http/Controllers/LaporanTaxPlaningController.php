<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanTaxPlaning;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;

class LaporanTaxPlaningController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanTaxPlaning::query();
    
        // Filter berdasarkan tanggal jika ada
        if (!empty($search)) {
            $query->where('tanggal', 'LIKE', "%$search%");
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
        $laporantaxplanings = $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"])
                                  ->paginate($perPage);
    

            // Ubah path gambar agar dapat diakses dari frontend
            $laporantaxplanings->getCollection()->transform(function ($item) {
            $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/accounting/taxplaning/{$item->gambar}"))
                ? asset("images/accounting/taxplaning/{$item->gambar}")
                : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
    
            return $item;
            });
        
            if ($request->ajax()) {
                return response()->json(['laporantaxplanings' => $laporantaxplanings]);
            }

        return view('accounting.taxplaning', compact('laporantaxplanings'));
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
                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedData['file_excel'] = $excelfilename;
            }
    
            if ($request->hasFile('gambar')) {
                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedData['gambar'] = $filename;
            }
    
            LaporanTaxPlaning::create($validatedData);
    
            return redirect()->route('taxplaning.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing taxplaning data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanTaxPlaning $taxplaning)
    {
        try {
            $fileRules = $taxplaning->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'mimes:xlsx,xls|max:2048';
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
                $destinationimages = "images/accounting/taxplaning/" . $taxplaning->gambar;
                if (File::exists($destinationimages)) {
                    File::delete($destinationimages);
                }

                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedData['gambar'] = $filename;
            }

            if ($request->hasFile('file_excel')) {
                $destinationfiles = "files/accounting/taxplaning/" . $taxplaning->file_excel;
                if (File::exists($destinationfiles)) {
                    File::delete($destinationfiles);
                }

                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedData['file_excel'] = $excelfilename;
            }

            $taxplaning->update($validatedData);

            return redirect()->route('taxplaning.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating taxplaning data: ' . $e->getMessage());
            return redirect()->route('taxplaning.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanTaxPlaning $taxplaning)
    {
        try {
            $destinationimages = "images/accounting/taxplaning/" . $taxplaning->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/taxplaning/" . $taxplaning->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $taxplaning->delete();
        
        return redirect()->route('taxplaning.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting taxplaning data: ' . $e->getMessage());
            return redirect()->route('taxplaning.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
            $laporans = LaporanTaxPlaning::where('tanggal', $validatedData['tanggal'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Laporan Tax Planning|');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/accounting/taxplaning/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/accounting/taxplaning/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
                }
    
                // Konten untuk setiap laporan
                $htmlContent = "
            <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                {$imageHTML}
                    <h3 style='margin: 0; padding: 0;'>Keterangan : {$laporan->keterangan}</h3>
                    <h3 style='margin: 0; padding: 0;'>Laporan : {$laporan->tanggal_formatted}</h3>
            </div>

                ";
           // Tambahkan ke PDF
           $mpdf->WriteHTML($htmlContent);
        }
            // Output PDF
            return response($mpdf->Output("Laporan_Tax Planning_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_PPN.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
