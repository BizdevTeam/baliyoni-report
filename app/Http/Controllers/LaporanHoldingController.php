<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHolding;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanHoldingController extends Controller
{
    // Show the view
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $laporanholdings = LaporanHolding::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporanholdings->sum('nilai');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporanholdings->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
            return $item->perusahaan . ' - ' . $formattedDate;
        })->toArray();        $data = $laporanholdings->pluck('nilai')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Laporan Holding', // Nama dataset
                    'text' => 'Nilai Holding', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('procurements.laporanholding', compact('laporanholdings', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'perusahaan' => [
                    'required',
                    Rule::in([
                        'PT. Baliyoni Saguna',
                        'CV. Elka Mandiri',
                        'PT. Naba Technology Solutions',
                        'CV. Bhirma Teknik',
                        'PT. Dwi Srikandi Nusantara',
                        'PT. Dwi Srikandi Indonesia'
                    ]),
                ],
                'nilai' => 'required|integer|min:0',
            ]);
    

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanHolding::where('bulan', $validatedata['bulan'])
            ->where('perusahaan', $validatedata['perusahaan'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
        
            LaporanHolding::create($validatedata);
    
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Holding Data: ' . $e->getMessage());
            Log::info('Perusahaan input:', [$request->input('perusahaan')]);
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanHolding $laporanholding)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'perusahaan' => [
                'required',
                Rule::in([
                    'PT. Baliyoni Saguna',
                    'CV. Elka Mandiri',
                    'PT. Naba Technology Solutions',
                    'CV. Bhirma Teknik',
                    'PT. Dwi Srikandi Nusantara',
                    'PT. Dwi Srikandi Indonesia'
                ]),
            ],
                'nilai' => 'required|integer|min:0',
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanHolding::where('bulan', $validatedData['bulan'])
                ->where('perusahaan', $validatedData['perusahaan'])
                ->where('id_holding', '!=', $laporanholding->id_holding)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
            
            $laporanholding->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanholding.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()
                ->route('laporanholding.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements|Halaman {PAGENO}');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Bulan</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Perusahaan</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Nilai Holding (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Holding</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);

    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_holding.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    public function destroy(LaporanHolding $laporanholding)
    {
        try {
            $laporanholding->delete();
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Holding Data: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanHoldingData()
    {
        $data = LaporanHolding::all(['bulan','perusahaan','nilai']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');

        // Ambil data dari database
        $laporanholdings = LaporanHolding::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Order by year (desc) and month (asc)
        ->get();  

        // Siapkan data untuk chart
        $labels = $laporanholdings->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
            return $item->perusahaan . ' - ' . $formattedDate;
        })->toArray();
        $data = $laporanholdings->pluck('nilai')->toArray();
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

