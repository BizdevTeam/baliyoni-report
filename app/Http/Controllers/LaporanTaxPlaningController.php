<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanTaxPlaning;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanTaxPlaningController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporantaxplanings = LaporanTaxPlaning::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan', 'like', "%$search%")
                         ->orWhere('keterangan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);
        return view('accounting.taxplaning', compact('laporantaxplanings'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => 'required|mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string|max:255'
            ]);
    
            if ($request->hasFile('file_excel')) {
                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedata['file_excel'] = $excelfilename;
            }
    
            if ($request->hasFile('gambar')) {
                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedata['gambar'] = $filename;
            }
    
            LaporanTaxPlaning::create($validatedata);
    
            return redirect()->route('taxplaning.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing taxplaning data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanTaxPlaning $taxplaning)
    {
        try {
            $fileRules = $taxplaning->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'required|mimes:xlsx,xls|max:2048';
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => $fileRules,
                'keterangan' => 'required|string|max:255'
            ]);

            if ($request->hasFile('gambar')) {
                $destinationimages = "images/accounting/taxplaning/" . $taxplaning->gambar;
                if (File::exists($destinationimages)) {
                    File::delete($destinationimages);
                }

                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedata['gambar'] = $filename;
            }

            if ($request->hasFile('file_excel')) {
                $destinationfiles = "files/accounting/taxplaning/" . $taxplaning->file_excel;
                if (File::exists($destinationfiles)) {
                    File::delete($destinationfiles);
                }

                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedata['file_excel'] = $excelfilename;
            }

            $taxplaning->update($validatedata);

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
            // Validasi input bulan
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
            ]);
    
            // Ambil data laporan berdasarkan bulan yang dipilih
            $laporans = LaporanTaxPlaning::where('bulan', $validatedata['bulan'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Tax Planning |Halaman {PAGENO}');
    
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
                    <h3 style='margin: 0; padding: 0;'>Laporan Bulan {$laporan->bulan}</h3>
                    <h3 style='margin: 0; padding: 0;'>Laporan Bulan {$laporan->bulan_formatted}</h3>
            </div>

                ";
           // Tambahkan ke PDF
           $mpdf->WriteHTML($htmlContent);
        }
            // Output PDF
            return response($mpdf->Output("Laporan_Tax Planning_{$laporan->bulan}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_PPN.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
