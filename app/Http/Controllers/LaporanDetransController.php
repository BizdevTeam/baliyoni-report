<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanDetrans;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;


class LaporanDetransController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
    
        $laporandetrans = LaporanDetrans::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->paginate($perPage);
    
        // Ambil semua bulan yang ada dalam data
        $months = $laporandetrans->sortBy('tanggal')->map(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
        })->unique()->values()->toArray();
        
        // Kelompokkan data berdasarkan pelaksana dan bulan tanpa akumulasi
        $groupedData = [];
        foreach ($laporandetrans as $item) {
            $month = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            $groupedData[$item->pelaksana][$month][] = $item->total_pengiriman; // Simpan sebagai array
        }
        
        // Siapkan warna untuk setiap pelaksana
        $colorMap = [
            'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
            'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
        ];
        $defaultColor = 'rgba(128, 128, 128, 0.7)';
        
        // Bangun datasets tanpa akumulasi
        $datasets = [];
        foreach ($groupedData as $pelaksana => $monthData) {
            $data = [];
            foreach ($months as $month) {
                $data[] = isset($monthData[$month]) ? array_sum($monthData[$month]) : 0; // Tidak akumulasi, hanya total per bulan
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
            //validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

        $errorMessage = '';
        if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
            return redirect()->back()->with('error', $errorMessage);
        }

        // Cek kombinasi unik date dan perusahaan
        $exists = LaporanDetrans::where('tanggal', $validatedData['tanggal'])
        ->where('pelaksana', $validatedData['pelaksana'])
        ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Data sudah ada.');
        }

        LaporanDetrans::create($validatedData);
    
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
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'Pengiriman Daerah Bali (SAMITRA)',
                        'Pengiriman Luar Daerah (DETRANS)',
                    ]),
                ],
                'total_pengiriman' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

          // Cek kombinasi unik date dan perusahaan
          $exists = LaporanDetrans::where('tanggal', $validatedData['tanggal'])
          ->where('pelaksana', $validatedData['pelaksana'])
          ->where('id_detrans', '!=', $laporandetran->id_detrans)->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
        }

        // Update data
        $laporandetran->update($validatedData);
    
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
            Log::error('Error updating Data: ' . $e->getMessage());
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
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Supports - Laporan Pengiriman|');

        // Konten HTML
        $htmlContent = "
        <div style='gap: 100px; width: 100%;'>
            <div style='width: 30%; float: left; padding-right: 20px;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
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
                <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Pengiriman</h2>
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
        $data = LaporanDetrans::all(['tanggal','total_pengiriman']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
{
    $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = LaporanDetrans::query();
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
        
        $laporandetrans = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();

    // Ambil semua bulan yang ada dalam data
    $months = $laporandetrans->sortBy('tanggal')->map(function ($item) {
        return \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
    })->unique()->values()->toArray();
    
    // Kelompokkan data berdasarkan pelaksana dan bulan tanpa akumulasi
    $groupedData = [];
    foreach ($laporandetrans as $item) {
        $month = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
        $groupedData[$item->pelaksana][$month][] = $item->total_pengiriman; // Simpan sebagai array
    }
    
    // Siapkan warna untuk setiap pelaksana
    $colorMap = [
        'Pengiriman Daerah Bali (SAMITRA)' => 'rgba(255, 0, 0, 0.7)',
        'Pengiriman Luar Daerah (DETRANS)' => 'rgba(0, 0, 0, 0.7)',
    ];
    $defaultColor = 'rgba(128, 128, 128, 0.7)';
    
    // Bangun datasets tanpa akumulasi
    $datasets = [];
    foreach ($groupedData as $pelaksana => $monthData) {
        $data = [];
        foreach ($months as $month) {
            $data[] = isset($monthData[$month]) ? array_sum($monthData[$month]) : 0; // Tidak akumulasi, hanya total per bulan
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
    
    // Kembalikan data dalam format JSON
    return response()->json($chartData);
}
}

