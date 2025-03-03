<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPenjualan;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Traits\DateValidationTrait;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Exception;

class RekapPenjualanController extends Controller
{
    use DateValidationTrait;
    // Show the view
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan date
        $rekappenjualans = RekapPenjualan::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage)
            ->withQueryString();

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $rekappenjualans->sum('total_penjualan');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7)
        {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });

        $labels = $rekappenjualans->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
            return $formattedDate;
        })->toArray();
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);

        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'text' => 'Total Penjualan', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];

        return view('marketings.rekappenjualan', compact('rekappenjualans', 'chartData'));
    }


    public function store(Request $request)
    {
        try {
            // Konversi tanggal agar selalu dalam format Y-m-d
            if ($request->has('tanggal')) {
                try {
                    $request->merge(['tanggal' => \Carbon\Carbon::parse($request->input('tanggal'))->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }

            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_penjualan' => 'required|integer|min:0',
            ]);

            // Validasi tanggal menggunakan trait
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek apakah data sudah ada (setelah validasi tanggal)
            if (RekapPenjualan::where('tanggal', $validatedData['tanggal'])->exists()) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Simpan ke database
            RekapPenjualan::create($validatedData);
            return redirect()->route('rekappenjualan.index')->with('success', 'Data Berhasil Ditambahkan');

        } catch (Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPenjualan $rekappenjualan)
    {
        try {
            // Konversi tanggal agar selalu dalam format Y-m-d
            if ($request->has('tanggal')) {
                try {
                    $request->merge(['tanggal' => \Carbon\Carbon::parse($request->input('tanggal'))->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }
    
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_penjualan' => 'required|integer|min:0',
            ]);
    
            // Validasi tanggal menggunakan trait
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            // Bersihkan total_penjualan agar hanya angka
            $validatedData['total_penjualan'] = preg_replace('/\D/', '', $validatedData['total_penjualan']);
    
            // Cek apakah tanggal sudah ada di database untuk perusahaan lain
            $exists = RekapPenjualan::where('tanggal', $validatedData['tanggal'])
                ->where('id_rp', '!=', $rekappenjualan->id_rp)
                ->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $rekappenjualan->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('rekappenjualan.index')->with('success', 'Data berhasil diperbarui.');
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
                ->route('rekappenjualan.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }    

    public function exportPDF(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);

            // Ambil data dari request
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);

            // Validasi isi tabel dan chart untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Rekap Penjualan|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // 
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



    public function destroy(RekapPenjualan $rekappenjualan)
    {
        try {
            $rekappenjualan->delete();
            return redirect()->route('rekappenjualan.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('rekappenjualan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');

        // Query RekapPenjualan with optional search filter
        $rekappenjualans = RekapPenjualan::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Order by year (desc) and month (asc)
            ->get(); // Fetch results as a collection

        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });

        // Format labels as "Month - Year"
        $labels = $rekappenjualans->map(function ($item) {
            return \Carbon\Carbon::parse($item->date)->translatedFormat('F - Y');
        })->toArray();

        // Numeric data for the chart
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();

        // Generate random background colors for each data point
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

        return response()->json([
            'labels' => $labels, // Properly formatted labels
            'datasets' => [
                [
                    'label' => 'Total Penjualan', // Label for the dataset
                    'data' => $data, // Numeric data for chart rendering
                    'backgroundColor' => $backgroundColors, // Random colors
                ],
            ],
        ]);
    }

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
}
