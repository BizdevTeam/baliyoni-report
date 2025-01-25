<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItMultimediaTiktok;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;

class ItMultimediaTiktokController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $itmultimediatiktoks = ItMultimediaTiktok::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        return view('it.mutimediatiktok', compact('itmultimediatiktoks'));
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
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedata['gambar'] = $filename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ItMultimediaTiktok::where('bulan', $validatedata['bulan'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            ItMultimediaTiktok::create($validatedata);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ItMultimediaTiktok $tiktok)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedata['gambar'] = $filename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ItMultimediaTiktok::where('bulan', $validatedata['bulan'])
                ->where('id_tiktok', '!=', $tiktok->id_tiktok)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            $tiktok->update($validatedata);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(ItMultimediaTiktok $tiktok)
    {
        try {
            $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $tiktok->delete();

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
                'images' => 'nullable|array',
            ]);

            $tableHTML = trim($data['table']);
            $imageSources = $data['images'] ?? [];

            // Validasi isi tabel untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($imageSources)) {
                return response()->json(['success' => false, 'message' => 'Data gambar kosong.'], 400);
            }
            if (!isset($data['images']) || !is_array($data['images'])) {
                $imageSources = [];
            } else {
                $imageSources = $data['images'];
            }

            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 15, // Margin bawah
                'format' => 'A4',
            ]);

            // Path gambar header
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing|Halaman {PAGENO}');

            // Konten tabel untuk PDF
            $htmlContent = "
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 5px;'>Bulan</th>
                                <th style='border: 1px solid #000; padding: 5px;'>Total Penjualan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
            ";

            // Tambahkan gambar di bawah tabel jika ada
            if (!empty($imageSources)) {
                foreach ($imageSources as $image) {
                    $htmlContent .= "
                        <div style='text-align: center; margin-top: 20px;'>
                            <img src='{$image}' style='width: 100%; height: auto;'>
                        </div>
                    ";
                }
            }

            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

            // Return PDF sebagai respon download
          return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_tiktok.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            Log::info('Isi tabel: ' . $tableHTML);
            Log::info('Jumlah gambar: ' . count($imageSources));
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

}
