<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanBizdev;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanBizdevController extends Controller
{
    use DateValidationTrait;
    // Show the view
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanbizdevs = LaporanBizdev::query()
            ->when($search, function ($query, $search) {
                return $query->where('date', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(date) DESC, MONTH(date) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

            if ($request->ajax()) {
                return response()->json(['laporanbizdevs' => $laporanbizdevs]);
            }

        return view('it.laporanbizdev', compact('laporanbizdevs'));    }

    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'date' => 'required|date',
                'aplikasi' => 'required',
                'kondisi_bulanlalu' => 'required',
                'kondisi_bulanini' => 'required',
                'update' => 'required',
                'rencana_implementasi' => 'required',
                'keterangan' => 'required',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            LaporanBizdev::create($validatedData);
    
            return redirect()->route('laporanbizdev.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Data Data: ' . $e->getMessage());
            return redirect()->route('laporanbizdev.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanBizdev $laporanbizdev)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'date' => 'required|date',
                'aplikasi' => 'required',
                'kondisi_bulanlalu' => 'required',
                'kondisi_bulanini' => 'required',
                'update' => 'required',
                'rencana_implementasi' => 'required',
                'keterangan' => 'required',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Update data
            $laporanbizdev->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('laporanbizdev.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Data: ' . $e->getMessage());
            return redirect()
                ->route('laporanbizdev.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan IT - Laporan Bizdev|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
                <div style='width: 100%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Aplikasi</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Kondisi Bulan Lalu</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Kondisi Bulan Ini</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Update</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Rencana Implementasi</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Keterangan</th>
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
                ->header('Content-Disposition', 'attachment; filename=\"laporan_bizdev.pdf\"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    public function destroy(LaporanBizdev $laporanbizdev)
    {
        try {
            $laporanbizdev->delete();
            return redirect()->route('laporanbizdev.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data Data: ' . $e->getMessage());
            return redirect()->route('laporanbizdev.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}

