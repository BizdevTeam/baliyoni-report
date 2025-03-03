<?php

namespace App\Http\Controllers;

use App\Models\LaporanPaketAdministrasi;
use App\Traits\DateValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanPaketAdministrasiController extends Controller
{
    use DateValidationTrait;

    // Menampilkan halaman utama
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan date
        $laporanpaketadministrasis = LaporanPaketAdministrasi::query()
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalPenjualan = $laporanpaketadministrasis->sum('total_paket');

        // Siapkan data untuk chart
        function getRandomRGBA($opacity = 0.7) {
            return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
        }
        
        $labels = $laporanpaketadministrasis->map(function($item) {
            $formattedDate = \Carbon\Carbon::parse($item->date)->translatedFormat('F Y');
            return $item->website . ' - ' . $formattedDate;
        })->toArray();
        $data = $laporanpaketadministrasis->pluck('total_paket')->toArray();
        
        // Generate random colors for each data item
        $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
        $chartData = [
            'labels' => $labels, // Labels untuk chart
            'datasets' => [
                [
                    'label' => 'Grafik Laporan Paket Administrasi', // Nama dataset
                    'text' => 'Total Paket', // Nama dataset
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => $backgroundColors, // Warna batang random
                ],
            ],
        ];
        
        return view('marketings.laporanpaketadministrasi',  compact('laporanpaketadministrasis', 'chartData'));    
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'website' => [
                    'required',
                    Rule::in([
                        'E - Katalog',
                        'E - Katalog Luar Bali',
                        'Balimall',
                        'Siplah'
                    ]),
                ],
                'total_paket' => 'required|integer|min:0',
            ]);
            
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanPaketAdministrasi::where('tanggal', $validatedData['tanggal'])
            ->where('website', $validatedData['website'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }
    
            LaporanPaketAdministrasi::create($validatedData);
            return redirect()->route('laporanpaketadministrasi.index')->with('success', 'Data Berhasil Ditambahkan');

        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            Log::info('Perusahaan input:', [$request->input('website')]);
            return redirect()->route('laporanpaketadministrasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanPaketAdministrasi $laporanpaketadministrasi)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'website' => [
                'required',
                Rule::in([
                    'E - Katalog',
                    'E - Katalog Luar Bali',
                    'Balimall',
                    'Siplah',
                ]),
            ],
                'total_paket' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanPaketAdministrasi::where('tanggal', $validatedData['tanggal'])
            ->where('website', $validatedData['website'])
            ->where('id_laporanpaket', '!=', $laporanpaketadministrasi->id_laporanpaket)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Tidak dapat diubah, data sudah ada.');
            }
    
            // Update data
            $laporanpaketadministrasi->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()
                ->route('laporanpaketadministrasi.index')
                ->with('success', 'Data berhasil diperbarui.');
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
                ->route('laporanpaketadministrasi.index')
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
    
            // Ambil data dari request dan trim spasi
            $tableHTML = trim($data['table']);
            $chartBase64 = trim($data['chart']);
    
            // Validasi isi tabel dan grafik untuk mencegah halaman kosong
            if (empty($tableHTML)) {
                return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
            }
            if (empty($chartBase64)) {
                return response()->json(['success' => false, 'message' => 'Data grafik kosong.'], 400);
            }
    
            // Buat instance mPDF dengan konfigurasi
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 35, // Tambahkan margin atas untuk header
                'margin_bottom' => 10, // Margin bawah
                'format'        => 'A4', // Ukuran kertas A4
            ]);
    
            // Siapkan header image jika file ada
            $headerImagePath = public_path('images/HEADER.png');
            if (file_exists($headerImagePath)) {
                $headerHtml = "
                    <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                        <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                    </div>
                ";
                $mpdf->SetHTMLHeader($headerHtml, 'O'); // Untuk halaman pertama dan seterusnya
            } else {
                Log::warning("Header image tidak ditemukan di path: {$headerImagePath}");
            }
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Marketing - Laporan Paket Administrasi|');
    
            // Buat konten HTML dengan styling CSS yang lebih ketat
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                                <th style='border: 1px solid #000; padding: 1px;'>Website</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Nilai Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Paket Administrasi</h2>
                    <img src='{$chartBase64}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
                </div>
            </div>
            ";
    
            // Tulis konten ke PDF
            $mpdf->WriteHTML($htmlContent);
    
            // Ambil output PDF sebagai string
            $pdfOutput = $mpdf->Output('', 'S');
    
            // Kembalikan PDF sebagai response download
            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="laporan_paket_administrasi.pdf"');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
        }
    }
    
    public function destroy(LaporanPaketAdministrasi $laporanpaketadministrasi)
    {
        try {
            $laporanpaketadministrasi->delete();
            return redirect()->route('laporanpaketadministrasi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('laporanpaketadministrasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function showChart(Request $request)
{
    // Ambil data dari database
    $search = $request->input('search');
        // Ambil data dari database
        $laporanpaketadministrasis = LaporanPaketAdministrasi::query()
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Order by year (desc) and month (asc)
        ->get();  
    // Siapkan data untuk chart
    $labels = $laporanpaketadministrasis->pluck('website')->toArray();
    $data = $laporanpaketadministrasis->pluck('total_paket')->toArray();
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

