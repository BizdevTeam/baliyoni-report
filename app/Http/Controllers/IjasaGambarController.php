<?php

namespace App\Http\Controllers;

use App\Models\IjasaGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Traits\DateValidationTrait;

class IjasaGambarController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $ijasagambars = IjasaGambar::query()
            ->when($search, function($query, $search) {
                return $query->where('date', 'like', "%$search%")
                             ->orWhere('keterangan', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(date) DESC, MONTH(date) ASC')
            ->paginate($perPage);
        return view('hrga.ijasagambar', compact('ijasagambars'));
    }

    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'date' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validateData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/hrga/ijasagambar'), $filename);
                $validateData['gambar'] = $filename;
            }

            IjasaGambar::create($validateData);

            return redirect()->route('ijasagambar.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('ijasagambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, IjasaGambar $ijasagambar)
    {
        try {
            $validateData = $request->validate([
                'date' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $destination = "images/hrga/ijasagambar/" . $ijasagambar->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/hrga/ijasagambar'), $filename);
                $validateData['gambar'] = $filename;
            }

            $ijasagambar->update($validateData);

            return redirect()->route('ijasagambar.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Instagram data: ' . $e->getMessage());
            return redirect()->route('ijasagambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(IjasaGambar $ijasagambar)
    {
        try {
            $destination = "images/hrga/ijasagambar/" . $ijasagambar->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $ijasagambar->delete();

            return redirect()->route('ijasagambar.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Instagram data: ' . $e->getMessage());
            return redirect()->route('ijasagambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input date
            $validateData = $request->validate([
                'date' => 'required|date',
            ]);
    
            // Ambil semua data laporan berdasarkan date yang dipilih
            $laporans = IjasaGambar::where('date', $validateData['date'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Bizdev Gambar|Halaman {PAGENO}');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/hrga/ijasagambar/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/hrga/ijasagambar/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
                }
    
                // Konten untuk setiap laporan
                $htmlContent = "
            <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                {$imageHTML}
                    <h3 style='margin: 0; padding: 0;'>Laporan Tanggal {$laporan->date_formatted}</h3>
                    <p style='margin: 0; padding: 0;'><strong>Keterangan:</strong> {$laporan->keterangan}</p>
            </div>

                ";
    
                // Tambahkan ke PDF
                $mpdf->WriteHTML($htmlContent);
            }
    
            // Output PDF
            return response($mpdf->Output("laporan_ijasa_gambar_{$validateData['date']}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_ijasa_gambar.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    

}
