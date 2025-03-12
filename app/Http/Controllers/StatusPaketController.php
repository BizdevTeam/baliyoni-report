<?php
namespace App\Http\Controllers;

use App\Models\StatusPaket;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class StatusPaketController extends Controller
{
    use DateValidationTrait;

    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan date
        $statuspakets = StatusPaket::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $statuspakets->sum('total_paket');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $statuspakets->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F - Y');
            return $item->status . ' - ' . $formattedDate;
        })->toArray();
        $data = $statuspakets->pluck('total_paket')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Status Paket', // Nama dataset
                    'text' => 'Total Jumlah Paket', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('marketings.statuspaket', compact('statuspakets', 'chartData'));    }

    public function store(Request $request)
    {
        try {
        // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'status' => [
                    'required',
                    Rule::in([
                        'Surat Pesanan',
                        'Surat Pertanggungjawaban',
                        'Keuangan',
                        'Dokumen Akhir',
                        'Finish',
                    ]),
                ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = StatusPaket::where('tanggal', $validatedData['tanggal'])
            ->where('status', $validatedData['status'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            StatusPaket::create($validatedData);
    
            return redirect()->route('statuspaket.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Status Paket: ' . $e->getMessage());
            Log::info('Perusahaan input:', [$request->input('status')]);
            return redirect()->route('statuspaket.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, StatusPaket $statuspaket)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'status' => [
                'required',
                Rule::in([
                    'Surat Pesanan',
                    'Surat Pertanggungjawaban',
                    'Keuangan',
                    'Dokumen Akhir',
                    'Finish',
                ]),
            ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = StatusPaket::where('tanggal', $validatedData['tanggal'])
            ->where('status', $validatedData['status'])
            ->where('id_statuspaket', '!=', $statuspaket->id_statuspaket)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Tidak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $statuspaket->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('statuspaket.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Status Paket: ' . $e->getMessage());
            return redirect()
                ->route('statuspaket.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Lapoan Status Paket|');

            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Website</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Nilai Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Status Paket</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_Status_paket.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }

    public function destroy(StatusPaket $statuspaket)
    {
        try {
            $statuspaket->delete();
            return redirect()->route('statuspaket.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Status Paket: ' . $e->getMessage());
            return redirect()->route('statuspaket.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function getLaporanPaketAdministrasiData()
    {
        $data = StatusPaket::all(['tanggal','status','total_paket']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        // Ambil data dari database
        $query = StatusPaket::query();
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
        
        $statuspakets = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();    

        // Format label sesuai kebutuhan
        $labels = $statuspakets->pluck('status')->toArray();
        $data = $statuspakets->pluck('total_paket')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);
    
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nilai Paket',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ]);
    }

    private function getRandomRGBAA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }

}

