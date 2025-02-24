<?php

namespace App\Http\Controllers;

use App\Models\ItMultimediaInstagram;
use App\Traits\DateValidationTrait;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ItMultimediaInstagramController extends Controller
{
    use DateValidationTrait;
    
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $itmultimediainstagrams = ItMultimediaInstagram::query()
            ->when($search, function($query, $search) {
                return $query->where('date', 'like', "%$search%")
                             ->orWhere('keterangan', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(date) DESC, MONTH(date) ASC')
            ->paginate($perPage);

        return view('it.multimediainstagram', compact('itmultimediainstagrams'));
    }

    public function store(Request $request)
    {
        try {
                // Konversi tanggal agar selalu dalam format Y-m-d
                if ($request->has('date')) {
                try {
                    $request->merge(['date' => \Carbon\Carbon::parse($request->date)->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }
            
            $validatedData = $request->validate([
                'date' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
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
                // Konversi tanggal agar selalu dalam format Y-m-d
                if ($request->has('date')) {
                try {
                    $request->merge(['date' => \Carbon\Carbon::parse($request->date)->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }
            $validatedData = $request->validate([
                'date' => 'required|date',
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
                'date' => 'required|date',
            ]);
    
            // Ambil semua data laporan berdasarkan date yang dipilih
            $laporans = ItMultimediaInstagram::where('date', $validatedData['date'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Multimedia Tiktok|Halaman {PAGENO}');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/it/multimediainstagram/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/it/multimediainstagram/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
                }
    
                // Konten untuk setiap laporan
                $htmlContent = "
            <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                {$imageHTML}
                    <h3 style='margin: 0; padding: 0;'>Keterangan : {$laporan->keterangan}</h3>
                    <h3 style='margin: 0; padding: 0;'>Laporan Tanggal {$laporan->bulan_formatted}</h3>
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
