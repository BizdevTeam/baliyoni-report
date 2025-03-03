<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\Perusahaan;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RekapPenjualanPerusahaanController extends Controller
{
    use DateValidationTrait;

    // Show the view
    public function index(Request $request)
    { 
        $perusahaans = Perusahaan::all();

        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query untuk mencari berdasarkan tahun dan date
        $rekappenjualanperusahaans = RekapPenjualanPerusahaan::with('perusahaan')
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%")
                         ->orWhereHas('perusahaan', function ($q) use ($search) {
                             $q->where('nama_perusahaan', 'LIKE', "%$search%");
                         });
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $rekappenjualanperusahaans->sum('total_penjualan');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $rekappenjualanperusahaans->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
            return $item->perusahaan->nama_perusahaan. ' - ' . $formattedDate;
        })->toArray();
        $data = $rekappenjualanperusahaans->pluck('total_penjualan')->toArray();
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Rekap Penjualan Perusahaan', // Nama dataset
                    'text' => 'Total Penjualan Perusahaan', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
            
        return view('marketings.rekappenjualanperusahaan', compact('rekappenjualanperusahaans', 'chartData', 'perusahaans'));    }

        public function store(Request $request)
        {
            try {
                // Validasi input
                $validatedData = $request->validate([
                    'tanggal' => 'required|date',
                    'perusahaan_id' => 'required|exists:perusahaans,id',
                    'total_penjualan' => 'required|integer|min:0',
                ]);

                // Validasi tanggal menggunakan trait
                $errorMessage = '';
                if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                    return redirect()->back()->with('error', $errorMessage);
                }

                // Cek kombinasi unik date dan perusahaan_id
                $exists = RekapPenjualanPerusahaan::where('tanggal', $validatedData['tanggal'])
                    ->where('perusahaan_id', $validatedData['perusahaan_id'])
                    ->exists();

                if ($exists) {
                    return redirect()->back()->with('error', 'Data sudah ada.');
                }

                // Simpan ke database
                RekapPenjualanPerusahaan::create($validatedData);
                return redirect()->route('rekappenjualanperusahaan.index')->with('success', 'Data Berhasil Ditambahkan');

            } catch (\Exception $e) {
                Log::error('Error Storing Laporan Holding Data: ' . $e->getMessage());
                return redirect()->route('rekappenjualanperusahaan.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
            }
        }

    public function update(Request $request, RekapPenjualanPerusahaan $rekappenjualanperusahaan)
    {
        try {   
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'perusahaan_id' => 'required|exists:perusahaans,id',
                'total_penjualan' => 'required|integer|min:0',
            ]);

            // Validasi tanggal menggunakan trait
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan_id
            $exists = RekapPenjualanPerusahaan::where('tanggal', $validatedData['tanggal'])
                ->where('perusahaan_id', $validatedData['perusahaan_id'])
                ->exists();

                if ($exists) {
                    return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
                }
    
            // Update data
            $rekappenjualanperusahaan->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('rekappenjualanperusahaan.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Pendapatan Perusahaan: ' . $e->getMessage());
            return redirect()->route('rekappenjualanperusahaan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Rekap Penjualan Perusahaan|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Perusahaan</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan Perusahaan</h2>
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
    
    public function destroy(RekapPenjualanPerusahaan $rekappenjualanperusahaan)
    {
        try {
            $rekappenjualanperusahaan->delete();
            return redirect()->route('rekappenjualanperusahaan.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('rekappenjualanperusahaan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');

        // Ambil data dari database
        $rekappenjualanperusahaans = RekapPenjualanPerusahaan::query()
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Order by year (desc) and month (asc)
        ->get();  

        // Siapkan data untuk chart
        $labels = $rekappenjualanperusahaans->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F - Y');
            return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
        })->toArray();
        $data = $rekappenjualanperusahaans->pluck('total_penjualan')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);
    
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Paket',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    
        // Kembalikan data dalam format JSON
        return response()->json($chartData);
    }
    
    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

}

