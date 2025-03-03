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
                return $query->where('tanggal', 'LIKE', "%$search%")
                             ->orWhereHas('perusahaan', function ($q) use ($search) {
                                 $q->where('nama_perusahaan', 'LIKE', "%$search%");
                             });
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);

        // Prepare chart data
        $labels = $laporanholdings->map(function ($item) {
            // Format the month using Carbon’s translatedFormat() as defined in your model accessor
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
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
                    'label'           => 'Grafik Laporan Holding',
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
            if ($request->has('tanggal')) {
                try {
                    $request->merge(['tanggal' => \Carbon\Carbon::parse($request->date)->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }

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

            if ($request->has('tanggal')) {
                try {
                    $request->merge(['tanggal' => \Carbon\Carbon::parse($request->date)->format('Y-m-d')]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Format tanggal tidak valid.');
                }
            }

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

            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Procurements - Laporan Pembelian (Holding)|');

            $htmlContent = "
            <div style='display: flex; gap: 20px; width: 100%;'>
                <div style='width: 30%;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
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
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Penjualan Perusahaan</h2>
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

        $laporanholdings = LaporanHolding::with('perusahaan')
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%")
                             ->orWhereHas('perusahaan', function ($q) use ($search) {
                                 $q->where('nama_perusahaan', 'LIKE', "%$search%");
                             });
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

        $labels = $laporanholdings->map(function ($item) {
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F - Y');
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
}
