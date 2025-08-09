<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanSPI;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class LaporanSPIController extends Controller
{
    use DateValidationTraitAccSPI;

    // Show the view
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $query = LaporanSPI::query();

        if ($request->filled('start_date')) {
            try {
                // Directly use the date string from the request.
                $startDate = $request->start_date;
                $query->whereDate('tanggal', '>=', $startDate);
            } catch (Exception $e) {
                Log::error("Invalid start_date format provided: " . $request->start_date);
            }
        }

        if ($request->filled('end_date')) {
            try {
                // Directly use the date string from the request.
                $endDate = $request->end_date;
                $query->whereDate('tanggal', '<=', $endDate);
            } catch (Exception $e) {
                Log::error("Invalid end_date format provided: " . $request->end_date);
            }
        }

        // Order the results and paginate, ensuring the correct filter parameters are kept.
        $laporanspis = $query
            ->orderBy('tanggal', 'asc')
            ->paginate($perPage)
            ->appends($request->only(['start_date', 'end_date', 'per_page']));

            if ($request->ajax()) {
                return response()->json(['laporanspis' => $laporanspis]);
            }

        return view('spi.laporanspi', compact('laporanspis'));    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi' => 'required',
                'implementasi' => 'required',
            ]);

            // // Cek kombinasi unik date dan perusahaan
            // $exists = LaporanSPI::where('tanggal', $validatedData['tanggal'])->exists();

            // if ($exists) {
            //     return redirect()->back()->with('error', 'Data Already Exists.');
            // }

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            LaporanSPI::create($validatedData);
            return redirect()->route('laporanspi.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporanspi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSPI $laporanspi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi' => 'required',
                'implementasi' => 'required',
            ]);
            
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Update data
            $laporanspi->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('laporanspi.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()
                ->route('laporanspi.index')
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
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan SPI - Operational SPI Report|');

        // Set CSS untuk memastikan formatting CKEditor dipertahankan
        $styleCSS = "
            ul, ol {
                padding-left: 20px;
                margin: 5px 0;
            }
            li {
                margin-bottom: 3px;
            }
            p {
                margin: 5px 0;
            }
            strong, b {
                font-weight: bold;
            }
            em, i {
                font-style: italic;
            }
        ";
        
        // Buat konten tabel dengan style tambahan untuk CKEditor
        $htmlContent = "
            <style>
                {$styleCSS}
            </style>
            <div style='width: 100%;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Aspects</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Problem</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Solution</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Implementation</th>
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
            ->header('Content-Disposition', 'attachment; filename=\"laporan_rekap_penjualan.pdf\"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    public function destroy(LaporanSPI $laporanspi)
    {
        try {
            $laporanspi->delete();
            return redirect()->route('laporanspi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporanspi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}

