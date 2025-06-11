<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHolding;
use App\Models\Perusahaan;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanHoldingController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    {
        $perusahaans = Perusahaan::all();

        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Retrieve LaporanHolding records along with the related Perusahaan
        $laporanholdings = LaporanHolding::with('perusahaan')
            ->when($search, function ($query, $search) {
                return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"])
                ->orWhereHas('perusahaan', function ($q) use ($search) {
                                 $q->where('nama_perusahaan', 'LIKE', "%$search%");
                             });
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);

        // Prepare chart data
        $labels = $laporanholdings->map(function ($item) {
            // Format the month using Carbon’s translatedFormat() as defined in your model accessor
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
        })->toArray();
        $data = $laporanholdings->pluck('nilai')->toArray();

        // Generate random colors for each data point
        $backgroundColors = array_map(function () {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 0.7);
        }, $data);

        $chartData = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Purchase Report Chart',
                    'data'            => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];

        return view('procurements.laporanholding', compact('laporanholdings', 'chartData', 'perusahaans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'perusahaan_id' => 'required|exists:perusahaans,id',
                'nilai' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan_id
            $exists = LaporanHolding::where('tanggal', $request->date)
                ->where('perusahaan_id', $request->perusahaan_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data untuk sudah ada');
            }

            LaporanHolding::create($validatedData);
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Holding Data: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanHolding $laporanholding)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'perusahaan_id' => 'required|exists:perusahaans,id',
                'nilai' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            // Cek apakah kombinasi date dan perusahaan_id sudah ada di data lain
            $exists = LaporanHolding::where('tanggal', $request->date)
                ->where('perusahaan_id', $request->perusahaan_id)
                ->where('id', '!=', $laporanholding->id) // Menggunakan model binding
                ->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data untuk sudah ada');
            }
    
            // Update data
            $laporanholding->update($validatedData);
    
            return redirect()->route('laporanholding.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanHolding $laporanholding)
    {
        try {
            $laporanholding->delete();

            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Laporan Holding Data: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF for the given laporanholding holding data.
     * Expects 'table' (HTML string) and 'chart' (base64 string) in the request.
     */
    public function exportPDF(Request $request)
    {
        try {
            $data = $request->validate([
                'table' => 'required|string',
                'chart' => 'required|string',
            ]);

            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);

            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }

            $mpdf = new \Mpdf\Mpdf([
                'orientation'  => 'L',
                'margin_left'  => 10,
                'margin_right' => 10,
                'margin_top'   => 35,
                'margin_bottom'=> 10,
                'format'       => 'A4',
            ]);

            $headerImagePath = public_path('images/HEADER.png');
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O');

            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Purchase Report (Holding)|');

            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Company</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Holding Value (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 60%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Purchase Report Chart</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";

            $mpdf->WriteHTML($htmlContent);

            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_perusahaan.pdf"');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    /**
     * Return all LaporanHolding data with related Perusahaan for API consumption.
     */
    public function getLaporanHoldingData()
    {
        $data = LaporanHolding::with('perusahaan')->get(['tanggal', 'perusahaan_id', 'nilai']);
        return response()->json($data);
    }

    /**
     * Provide chart data in JSON format.
     */
    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $query = LaporanHolding::query();
        
        // Filter berdasarkan tanggal jika ada
        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }
        
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if ($startMonth && $endMonth) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
            
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $laporanholdings = $query
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->get();

        $labels = $laporanholdings->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            return $item->perusahaan->nama_perusahaan . ' - ' . $formattedDate;
        })->toArray();
        $data = $laporanholdings->pluck('nilai')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);

        $chartData = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Total Paket',
                    'data'            => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];

        return response()->json($chartData);
    }

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

    public function chartTotal(Request $request)
    {
    $search = $request->input('search');
    $startMonth = $request->input('start_month');
    $endMonth = $request->input('end_month');

    // Ambil data dari database
    $query = LaporanHolding::query();
    
    // Filter berdasarkan tanggal jika ada
    if ($search) {
        $query->where('tanggal', 'LIKE', "%$search%");
    }
    
    // Filter berdasarkan range bulan-tahun jika keduanya diisi
    if ($startMonth && $endMonth) {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
        
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
    
    $laporanholdings = $query->get();

    // Akumulasi total penjualan berdasarkan nama website
    $akumulasiData = [];
    foreach ($laporanholdings as $item) {
        $namaPerusahaan = $item->perusahaan->nama_perusahaan;
        if (!isset($akumulasiData[$namaPerusahaan])) {
            $akumulasiData[$namaPerusahaan] = 0;
        }
        $akumulasiData[$namaPerusahaan] += $item->nilai;
    }

    // Siapkan data untuk chart
    $labels = array_keys($akumulasiData);
    $data = array_values($akumulasiData);
    $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);

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

private function getRandomRGBAA1($opacity = 0.7)
{
    return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
}

}
