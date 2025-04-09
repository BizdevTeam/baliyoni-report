<?php

namespace App\Http\Controllers;

use App\Models\LaporanBizdevGambar;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class LaporanBizdevGambarController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanBizdevGambar::query();
    
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
        $laporanbizdevgambars = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

        // Ubah path gambar agar dapat diakses dari frontend
        $laporanbizdevgambars->getCollection()->transform(function ($item) {
        $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/it/laporanbizdevgambar/{$item->gambar}"))
            ? asset("images/it/laporanbizdevgambar/{$item->gambar}")
            : asset("images/no-image.png"); // Placeholder jika tidak ada gambar

            return $item;
            });
        
            if ($request->ajax()) {
                return response()->json(['laporanbizdevgambars' => $laporanbizdevgambars]);
            }

        return view('it.laporanbizdevgambar', compact('laporanbizdevgambars'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kendala' => 'required|string',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/laporanbizdevgambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            LaporanBizdevGambar::create($validatedData);

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanBizdevGambar $laporanbizdevgambar)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'kendala' => 'required|string',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('gambar')) {
                $destination = "images/it/laporanbizdevgambar/" . $laporanbizdevgambar->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/laporanbizdevgambar'), $filename);
                $validatedData['gambar'] = $filename;
            }

            $laporanbizdevgambar->update($validatedData);

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanBizdevGambar $laporanbizdevgambar)
    {
        try {
            $destination = "images/it/laporanbizdevgambar/" . $laporanbizdevgambar->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $laporanbizdevgambar->delete();

            return redirect()->route('laporanbizdevgambar.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Instagram data: ' . $e->getMessage());
            return redirect()->route('laporanbizdevgambar.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
        $laporans = LaporanBizdevGambar::where('tanggal', $validatedData['tanggal'])->get();

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
        $mpdf->SetHTMLHeader("<div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
            <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
        </div>", 'O');

        // Tambahkan footer
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Laporan Bizdev Gambar|');

        // Loop melalui setiap laporan dan tambahkan ke PDF
        foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->gambar) && file_exists(public_path("images/it/laporanbizdevgambar/{$laporan->gambar}"))) {
                $imagePath = public_path("images/it/laporanbizdevgambar/{$laporan->gambar}");
                $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; text-align:center;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
            }

            // Konten untuk setiap laporan
            $htmlContent = "
                <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
                    {$imageHTML}
                </div>
            ";
            
            // Tambahkan ke PDF
            $mpdf->WriteHTML($htmlContent);
        }

        // Tambahkan halaman baru untuk tabel kendala dan tanggal
        $mpdf->AddPage();
        $tableContent = "<h2 style='text-align: center;'>Daftar Kendala</h2>
            <table border='1' style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th style='padding: 8px; background-color: #f2f2f2;'>Kendala</th>
                        <th style='padding: 8px; background-color: #f2f2f2;'>Tanggal</th>
                    </tr>
                </thead>
                <tbody>";
        
        foreach ($laporans as $laporan) {
            $tableContent .= "<tr>
                <td style='padding: 8px;'>" . $laporan->tanggal_formatted . "</td>
                <td style='padding: 8px;'>" . $laporan->kendala . "</td>
            </tr>";
        }

        $tableContent .= "</tbody></table>";
        $mpdf->WriteHTML($tableContent);

        // Output PDF
        return response($mpdf->Output("laporan_bizdev_gambar_{$validatedData['tanggal']}.pdf", 'D'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_bizdev_gambar.pdf"');
    
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}

}
