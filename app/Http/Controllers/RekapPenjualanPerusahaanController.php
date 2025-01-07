<?php

namespace App\Http\Controllers;

use App\Models\RekapPenjualanPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;


class RekapPenjualanPerusahaanController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('marketings.rekappenjualanperusahaan');
    }

    // Fetch data dengan filter
    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');
            $query = RekapPenjualanPerusahaan::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'perusahaan' => 'required|array|min:1',
            'perusahaan.*' => 'required|string|max:255',
            'nilai_paket' => 'required|array|min:1',
            'nilai_paket.*' => 'required|numeric|min:0',
        ]);

        try {   
            $dataToInsert = $this->prepareDataForInsert($validated);

            RekapPenjualanPerusahaan::insert($dataToInsert);

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    // Perbarui data
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'perusahaan' => 'required|array|min:1',
            'perusahaan.*' => 'required|string|max:255',
            'nilai_paket' => 'required|array|min:1',
            'nilai_paket.*' => 'required|numeric|min:0',
        ]);

        try {
            // Hapus data lama untuk perusahaan terkait
            RekapPenjualanPerusahaan::where('id', $id)->delete();

            $dataToInsert = $this->prepareDataForInsert($validated);
            RekapPenjualanPerusahaan::insert($dataToInsert);

            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error updating data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui data.'], 500);
        }
    }

    // Hapus data
    public function destroy($id)
    {
        try {
            $paket = RekapPenjualanPerusahaan::findOrFail($id);
            $paket->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Data not found: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $data = $request->all();
            $tableHTML = $data['table'];
            $chartBase64 = $data['chart'];
    
            // Create mPDF instance with landscape orientation and margins
            $mpdf = new Mpdf([
                'orientation' => 'L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'format' => 'A4', // Set paper size to A4
            ]);
    
            // Prepare HTML for table
            $tableHTMLContent = "
                <h1 style='text-align:center;'>Rekap Penjualan Perusahaan</h1>
                <h2>Data Tabel</h2>
                <table style='border-collapse: collapse; width: 100%;' border='1'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #000; padding: 8px;'>Bulan/Tahun</th>
                            <th style='border: 1px solid #000; padding: 8px;'>Perusahaan</th>
                            <th style='border: 1px solid #000; padding: 8px;'>Nilai Paket</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>    
                </table>
            ";
    
            // Prepare HTML for chart
            $chartHTMLContent = "
                <h1 style='text-align:center;'>Rekap Penjualan Perusahaan</h1>
                <h2>Grafik Penjualan</h2>
                <div style='text-align: center;'>
                    <img src='{$chartBase64}' alt='Chart' style='width: 100%; max-width: 100%; height: auto;' />
                </div>
            ";
                   // Write table content to the first page
        $mpdf->WriteHTML($tableHTMLContent);

        // Add a new page for the chart
        $mpdf->AddPage();

        // Write chart content to the second page
        $mpdf->WriteHTML($chartHTMLContent);

        // Output as downloadable PDF
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="rekap_penjualan_perusahaan.pdf"');
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.']);
    }
}

    // Persiapkan data untuk di-insert ke database
    private function prepareDataForInsert($validated)
    {
        $dataToInsert = [];
        foreach ($validated['perusahaan'] as $index => $perusahaan) {
            $dataToInsert[] = [
                'bulan_tahun' => $validated['bulan_tahun'],
                'perusahaan' => $perusahaan,
                'nilai_paket' => $validated['nilai_paket'][$index],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $dataToInsert;
    }
}
