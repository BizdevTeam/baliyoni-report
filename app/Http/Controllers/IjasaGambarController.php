<?php

namespace App\Http\Controllers;

use App\Models\IjasaGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Traits\DateValidationTrait;
use Carbon\Carbon;
use Exception;

class IjasaGambarController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = IjasaGambar::query();
    
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
        $ijasagambars = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);
            
              // Ubah path gambar agar dapat diakses dari frontend
              $ijasagambars->getCollection()->transform(function ($item) {
                $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/hrga/ijasagambar/{$item->gambar}"))
                    ? asset("images/hrga/ijasagambar/{$item->gambar}")
                    : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
                return $item;
            });
        
            if ($request->ajax()) {
                return response()->json(['ijasagambars' => $ijasagambars]);
            }

        return view('hrga.ijasagambar', compact('ijasagambars'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/hrga/ijasagambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            IjasaGambar::create($validatedData);

            return redirect()->route('ijasagambar.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('ijasagambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, IjasaGambar $ijasagambar)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $destination = "images/hrga/ijasagambar/" . $ijasagambar->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/hrga/ijasagambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            $ijasagambar->update($validatedData);

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
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
            ]);
    
            // Ambil semua data laporan berdasarkan date yang dipilih
            $laporans = IjasaGambar::where('tanggal', $validatedData['tanggal'])->get();
    
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - iJASA Report Picture|');
    
            // Loop melalui setiap laporan dan tambahkan ke PDF
            foreach ($laporans as $index => $laporan) {
                $imageHTML = '';
    
                if (!empty($laporan->gambar) && file_exists(public_path("images/hrga/ijasagambar/{$laporan->gambar}"))) {
                    $imagePath = public_path("images/hrga/ijasagambar/{$laporan->gambar}");
                    $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
                } else {
                    $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>File not found</p>";
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
            return response($mpdf->Output("laporan_ijasa_gambar_{$validatedData['tanggal']}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_ijasa_gambar.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    

}
