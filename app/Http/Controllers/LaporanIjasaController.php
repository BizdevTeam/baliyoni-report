<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaporanIjasaController extends Controller
{
        public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanijasas = LaporanIjasa::query()
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%")
                         ->orWhere('permasalahan', 'LIKE', "%$search%");
        })
        ->orderBy('tanggal', 'DESC')
        ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json(['laporanijasas' => $laporanijasas]);
        }
    
    return view('hrga.laporanijasa', compact('laporanijasas'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|date_format:H:i',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i',
                'gambar.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2MB per gambar
            ]);

            $gambarPaths = [];

            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $gambar) {
                    $filename = time() . '_' . $gambar->getClientOriginalName();
                    $path = $gambar->storeAs('images/hrga/laporanijasa', $filename, 'public');
                    $gambarPaths[] = $path;
                }
            }

            // Simpan laporan beserta gambar dalam format JSON
            $laporan = LaporanIjasa::create(array_merge($validatedata, [
                'gambar' => json_encode($gambarPaths)
            ]));

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanIjasa $laporanijasa)
    {
        try {
            $validatedata = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'nullable|date_format:H:i',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i',
                'gambar.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $gambarPaths = json_decode($laporanijasa->gambar, true) ?? [];

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama
                foreach ($gambarPaths as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                // Simpan gambar baru
                $gambarPaths = [];
                foreach ($request->file('gambar') as $gambar) {
                    $filename = time() . '_' . $gambar->getClientOriginalName();
                    $path = $gambar->storeAs('images/hrga/laporanijasa', $filename, 'public');
                    $gambarPaths[] = $path;
                }
            }

            $laporanijasa->update(array_merge($validatedata, [
                'gambar' => json_encode($gambarPaths)
            ]));

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function exportPDF(Request $request)
{
    try {
        $data = $request->validate([
            'table' => 'required|string',
        ]);

        $tableHTML = trim($data['table']);
        $laporan = LaporanIjasa::all();

        if (empty($tableHTML)) {
            return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
        }

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L', // Landscape orientation
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35, // Tambahkan margin atas untuk header teks
            'margin_bottom' => 20, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - iJASA|Halaman {PAGENO}');

        $imageHTML = '';
        foreach ($laporan as $item) {
            if (!empty($item->gambar)) {
                $gambarPaths = json_decode($item->gambar, true);
                if (is_array($gambarPaths)) {
                    foreach ($gambarPaths as $path) {
                        $imagePath = public_path("storage/{$path}");
                        if (file_exists($imagePath)) {
                            $imageHTML .= "<div style='text-align: center;'><img src='{$imagePath}' style='width: 100%; height: auto;'/></div>";
                        } else {
                            $imageHTML .= "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
                        }
                    }
                }
            }
        }

        $htmlContent = "
            <div style='width: 100%;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Jam</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Permasalahan</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Impact</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Troubleshooting</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Tanggal</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
            <div style='text-align: center;'>
                {$imageHTML}
            </div>
        ";

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_ijasa.pdf"');
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}

    public function destroy(LaporanIjasa $laporanijasa)
    {
        try {
            $gambarPaths = json_decode($laporanijasa->gambar, true) ?? [];

            foreach ($gambarPaths as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }

            $laporanijasa->delete();

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
