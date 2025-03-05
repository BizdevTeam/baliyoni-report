<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPiutangServisAsp;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Traits\DateValidationTrait;

class RekapPiutangServisAspController extends Controller
{
    use DateValidationTrait;
    // Show the view
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan date
        $rekappiutangservisasps = RekapPiutangServisAsp::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%")
                             ->orWhere('pelaksana', 'like', "%$search%");
                return $query->where('tanggal', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $rekappiutangservisasps->sum('nilai_piutang');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $rekappiutangservisasps->pluck('pelaksana')->toArray();
        $data = $rekappiutangservisasps->pluck('nilai_piutang')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Rekap Pendapatan Servis ASP', // Nama dataset
                    'text' => 'Nilai Pendapatan Servis ASP', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('supports.rekappiutangservisasp', compact('rekappiutangservisasps', 'chartData'));    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                    'required',
                    Rule::in([
                    'CV. ARI DISTRIBUTION CENTER',
                    'CV. BALIYONI COMPUTER',
                    'PT. NABA TECHNOLOGY SOLUTIONS',
                    'CV. ELKA MANDIRI (50%)-SAMITRA',
                    'CV. ELKA MANDIRI (50%)-DETRAN'
                    ]),
                ],
                'nilai_piutang' => 'required|integer|min:0',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPiutangServisASP::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            RekapPiutangServisAsp::create($validatedData);
    
            return redirect()->route('rekappiutangservisasp.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Rekap Pendapatan Servis ASP Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('rekappiutangservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    
    public function update(Request $request, RekapPiutangServisAsp $rekappiutangservisasp)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'pelaksana' => [
                'required',
                Rule::in([
                    'CV. ARI DISTRIBUTION CENTER',
                    'CV. BALIYONI COMPUTER',
                    'PT. NABA TECHNOLOGY SOLUTIONS',
                    'CV. ELKA MANDIRI (50%)-SAMITRA',
                    'CV. ELKA MANDIRI (50%)-DETRAN'
                ]),
            ],
                'nilai_piutang' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            $exists = RekapPiutangServisAsp::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->where('id_rpiutangsasp', '!=', $rekappiutangservisasp->id_rpiutangsasp)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'TIdak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $rekappiutangservisasp->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('rekappiutangservisasp.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

            // Cek kombinasi unik date dan perusahaan
            $exists = RekapPiutangServisASP::where('tanggal', $validatedData['tanggal'])
            ->where('pelaksana', $validatedData['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Update data rekappiutang
            $rpiutangsasp->update($validatedData);

            return redirect()->route('rpiutangsasp.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Holding: ' . $e->getMessage());
            return redirect()
                ->route('rekappiutangservisasp.index')
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Support - Laporan Piutang Servis ASP|');
    
            // Buat konten tabel dengan gaya CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Pelaksana</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Nilai Piutang (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 45%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Piutang Servis ASP</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
            // Tambahkan konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Return PDF sebagai respon download
            return response($mpdf->Output('', 'S'), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="laporan_rekap_penjualan_perusahaan.pdf"');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }   

    public function destroy(RekapPiutangServisAsp $rekappiutangservisasp)
    {
        try {
            $rekappiutangservisasp->delete();
            return redirect()->route('rekappiutangservisasp.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Pendapatan Servis ASP Data: ' . $e->getMessage());
            return redirect()->route('rekappiutangservisasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function getRekapPenjualaPerusahaannData()
    {
        $data = RekapPiutangServisAsp::all(['tanggal','pelaksana','nilai_piutang']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)
    {
        $search = $request->input('search');
    
        // Ambil data dari database
        $rekappiutangservisasps = RekapPiutangServisAsp::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Order by year (desc) and month (asc)
            ->get();  
    
        // Siapkan data untuk chart
        $labels = $rekappiutangservisasps->pluck('pelaksana')->toArray(); // Nama pelaksana
        $data = $rekappiutangservisasps->pluck('nilai_piutang')->toArray(); // Nilai pendapatan
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data); // Warna acak untuk pie chart
    
        // Format data untuk Pie Chart
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Grafik Rekap Pendapatan Servis ASP',
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

