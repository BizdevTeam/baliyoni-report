<?php

namespace App\Http\Controllers;

use App\Models\LaporanPpn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LaporanPpnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanppns = LaporanPpn::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);
        return view('accounting.laporanppn', compact('laporanppns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'required|mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);

            if ($request->hasFile('file')) {
                $excelFileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $excelFileName);
                $validatedata['file'] = $excelFileName;
            }

            if ($request->hasFile('thumbnail')) {
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedata['thumbnail'] = $fileName;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanPpn::where('bulan', $validatedata['bulan'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            LaporanPpn::create($validatedata);
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
            $validatedata = $request->validate([
                'bulan' => 'required|string',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);

            if ($request->hasFile('thumbnail')) {
                $destinationImages = "images/accounting/ppn/" . $laporanppn->thumbnail;
                if (File::exists($destinationImages)) {
                    File::delete($destinationImages);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedata['thumbnail'] = $fileName;
            }

            if ($request->hasFile('file')) {
                $destinationFile = "files/accounting/ppn/" . $laporanppn->file;
                if (File::exists($destinationFile)) {
                    File::delete($destinationFile);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $fileName);
                $validatedata['file'] = $fileName;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanPpn::where('bulan', $validatedata['bulan'])
            ->where('id_laporanppn', '!=', $laporanppn->id_laporanppn)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            $laporanppn->update($validatedata);
            
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
            // Validasi input bulan
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
            ]);
    
            // Ambil data laporan berdasarkan bulan yang dipilih
            $laporan = LaporanPpn::where('bulan', $validatedata['bulan'])->first();
    
            if (!$laporan) {
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - PPN |Halaman {PAGENO}');
    
            // Cek apakah ada thumbnail yang di-upload
            $imageHTML = '';
            if (!empty($laporan->thumbnail) && file_exists(public_path("images/accounting/ppn/{$laporan->thumbnail}"))) {
                $imagePath = public_path("images/accounting/ppn/{$laporan->thumbnail}");
                $imageHTML = "<img src='{$imagePath}' style='width: 100%; height: auto;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
            }
    
            // Konten PDF
            $htmlContent = "
                <div style='text-align: center;'>
                    {$imageHTML}
                </div>
            ";
    
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Output PDF
            return response($mpdf->Output("Laporan_PPn_{$laporan->bulan}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_PPN.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

}
