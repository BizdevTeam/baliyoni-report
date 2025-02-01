<?php

namespace App\Http\Controllers;

use App\Models\ItMultimediaInstagram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ItMultimediaInstagramController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $itmultimediainstagrams = ItMultimediaInstagram::query()
            ->when($search, function($query, $search) {
                return $query->where('bulan', 'like', "%$search%")
                             ->orWhere('keterangan', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        return view('it.multimediainstagram', compact('itmultimediainstagrams'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediainstagram'), $filename);
                $validatedata['gambar'] = $filename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ItMultimediaInstagram::where('bulan', $validatedata['bulan'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            ItMultimediaInstagram::create($validatedata);

            return redirect()->route('multimediainstagram.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('multimediainstagram.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ItMultimediaInstagram $multimediainstagram)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $destination = "images/it/multimediainstagram/" . $multimediainstagram->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediainstagram'), $filename);
                $validatedata['gambar'] = $filename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ItMultimediaInstagram::where('bulan', $validatedata['bulan'])
                ->where('id_instagram', '!=', $multimediainstagram->id_instagram)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            $multimediainstagram->update($validatedata);

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
            // Validasi input bulan
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
            ]);
    
            // Ambil data laporan berdasarkan bulan yang dipilih
            $laporan = ItMultimediaInstagram::where('bulan', $validatedata['bulan'])->first();
    
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
    
            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Multimedia Instagram|Halaman {PAGENO}');
    
            // Cek apakah ada gambar yang di-upload
            $imageHTML = '';
            if (!empty($laporan->gambar) && file_exists(public_path("images/it/multimediainstagram/{$laporan->gambar}"))) {
                $imagePath = public_path("images/it/multimediainstagram/{$laporan->gambar}");
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
            return response($mpdf->Output("laporan_multimedia_instagram{$laporan->bulan}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Laba_Rugi.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
