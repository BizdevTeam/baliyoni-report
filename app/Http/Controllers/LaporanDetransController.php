<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanDetrans;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;


class LaporanDetransController extends Controller
{
    public function index(Request $request)
{ 
    $perPage = $request->input('per_page', 12);
    $search = $request->input('search');

    $laporandetrans = LaporanDetrans::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

    // Calculate total (if needed)
    $totalPengiriman = $laporandetrans->sum('total_pengiriman');
    $months = $laporandetrans->sortBy('bulan')->map(function ($item) {
        return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
    })->unique()->values()->toArray();
    
    // Kelompokkan data berdasarkan pelaksana dan bulan
    $groupedData = [];
    foreach ($laporandetrans as $item) {
        $month = \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        $groupedData[$item->pelaksana][$month] = $item->total_pengiriman;
    }
    
    // Siapkan warna untuk setiap pelaksana
    $colorMap = [
        'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
        'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
    ];
    $defaultColor = 'rgba(128, 128, 128, 0.7)';
    
    // Bangun datasets
    $datasets = [];
    foreach ($groupedData as $pelaksana => $monthData) {
        $data = [];
        foreach ($months as $month) {
            $data[] = $monthData[$month] ?? 0; // Isi 0 jika data bulan tidak ada
        }
        
        $datasets[] = [
            'label' => $pelaksana,
            'data' => $data,
            'backgroundColor' => $colorMap[$pelaksana] ?? $defaultColor,
        ];
    }
    
    $chartData = [
        'labels' => $months,
        'datasets' => $datasets,
    ];

    return view('supports.laporandetrans', compact('laporandetrans', 'chartData'));
}

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

       // Cek kombinasi unik bulan dan perusahaan
       $exists = LaporanDetrans::where('bulan', $validatedata['bulan'])
       ->where('pelaksana', $validatedata['pelaksana'])
       ->exists();

       if ($exists) {
           return redirect()->back()->with('error', 'Data Already Exists.');
       }

    
            LaporanDetrans::create($validatedata);
    
            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Detrans Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanDetrans $laporandetran)
    {
        try {
            // Validasi input
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

          // Cek kombinasi unik bulan dan perusahaan
          $exists = LaporanDetrans::where('bulan', $validatedata['bulan'])
          ->where('pelaksana', $validatedata['pelaksana'])
          ->where('id_detrans', '!=', $laporandetran->id_detrans)->exists();

          if ($exists) {
              return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
          }
  

            // Update data
            $laporandetran->update($validatedata);
    
            // Redirect dengan pesan sukses
            return redirect()->route('laporandetrans.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Detrans Data: ' . $e->getMessage());
            return redirect()
                ->route('laporandetrans.index')
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
            'margin_top' => 35, // Kurangi margin atas
            'margin_bottom' => 10, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);

        // Tambahkan header ke PDF
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Supports|Halaman {PAGENO}');

        // Konten HTML
        $htmlContent = "
        <div style='gap: 100px; width: 100%;'>
            <div style='width: 30%; float: left; padding-right: 20px;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Bulan</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Pelaksana</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Total Pengiriman (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
            <div style='width: 65%; text-align:center; margin-left: 20px;'>
                <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Pengiriman Detrans</h2>
                <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Penjualan' />
            </div>
        </div>
        ";
        // Tambahkan konten ke PDF
        $mpdf->WriteHTML($htmlContent);

        // Return PDF sebagai respon download
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_pengiriman_detrans.pdf"');
    } catch (\Exception $e) {
        // Log error jika terjadi masalah
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}

    public function destroy(LaporanDetrans $laporandetran)
    {
        try {
            $laporandetran->delete();
            return redirect()->route('laporandetrans.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Detrans Data Data: ' . $e->getMessage());
            return redirect()->route('laporandetrans.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanSamitraData()
    {
        $data = LaporanDetrans::all(['bulan','total_pengiriman']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');

        // Ambil data dari database
        $laporandetrans = LaporanDetrans::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Order by year (desc) and month (asc)
        ->get();  

        // Siapkan data untuk chart
        $labels = $laporandetrans->map(function ($item) {
            return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        })->toArray();
    
        $data = $laporandetrans->pluck('total_pengiriman')->toArray();
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

