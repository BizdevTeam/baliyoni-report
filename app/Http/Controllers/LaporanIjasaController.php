<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                'resolve_jam' => 'required|date_format:H:i'
            ]);
    
            LaporanIjasa::create($validatedata);
    
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
                'resolve_jam' => 'required|date_format:H:i'
            ]);
    
            $laporanijasa->update($validatedata);
    
            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
            ]);
    
            // Ambil data dari request
            $tableHTML = trim($data['table']);
    
            // Validasi isi tabel untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
    
            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 10, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);
    
            // Tambahkan gambar sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA|Halaman {PAGENO}');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Jam</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Permasalahan</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Impact</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Troubleshooting</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Resolve Tanggal</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Resolve Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
            ";
    
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=\"laporan_ijasa.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }


    public function destroy(LaporanIjasa $laporanijasa)
    {
        $laporanijasa->delete();

        return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Dihapus');
    }
}
