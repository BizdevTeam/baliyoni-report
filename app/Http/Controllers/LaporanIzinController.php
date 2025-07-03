<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIzin;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class LaporanIzinController extends Controller
{
    use DateValidationTrait;
    // Show the view
    // public function index(Request $request)
    // { 
    //     $perPage = $request->input('per_page', 12);
    //     $search = $request->input('search');

    //     #$query = KasHutangPiutang::query();

    //     // Query untuk mencari berdasarkan tahun dan date
    //     $laporanizins = LaporanIzin::query()
    //         ->when($search, function ($query, $search) {
    //             return $query->where('tanggal', 'LIKE', "%$search%")
    //                          ->orWhere('nama', 'like', "%$search%");
    //         })
    //         ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC') // Urutkan berdasarkan tahun (descending) dan date (ascending)
    //         ->paginate($perPage);

    //     // Hitung total untuk masing-masing kategori
    //     $totalPenjualan = $laporanizins->sum('total_izin');

    //     // Siapkan data untuk chart
    //     function getRandomRGBA($opacity = 0.7) {
    //         return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    //     }
        
    //     $labels = $laporanizins->map(function($item) {
    //         $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F Y');
    //         return $item->nama. ' - ' .$formattedDate;
    //     })->toArray();        
    //     $data = $laporanizins->pluck('total_izin')->toArray();
        
    //     // Generate random colors for each data item
    //     $backgroundColors = array_map(fn() => getRandomRGBA(), $data);
        
    //     $chartData = [
    //         'labels' => $labels, // Labels untuk chart
    //         'datasets' => [
    //             [
    //                 'label' => 'Grafik Laporan Sakit', // Nama dataset
    //                 'text' => 'Total Izin', // Nama dataset
    //                 'data' => $data, // Data untuk chart
    //                 'backgroundColor' => $backgroundColors, // Warna batang random
    //             ],
    //         ],
    //     ];
        
    //     return view('hrga.laporanizin', compact('laporanizins', 'chartData'));    }
     public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        // Query dasar untuk digunakan kembali
        $baseQuery = LaporanIzin::query()
            ->when($search, function ($query, $search) {
                $query->where('tanggal', 'LIKE', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
            });

        // [FIX] Ambil SEMUA data untuk analisis dan chart agar akurat
        $allLeaveReports = (clone $baseQuery)->orderBy('tanggal', 'asc')->get();

        // Ambil data yang DIPAGINASI hanya untuk tampilan tabel
        $laporanizins = (clone $baseQuery)->orderBy('tanggal', 'desc')->paginate($perPage);

        // [FIX] Siapkan data chart dari SEMUA data
        $labels = $allLeaveReports->map(function($item) {
            $formattedDate = Carbon::parse($item->tanggal)->translatedFormat('F Y');
            return $item->nama. ' - ' . $formattedDate;
        })->all();
        
        // [FIX] Gunakan kolom 'total_izin'
        $data = $allLeaveReports->pluck('total_izin')->all();
        
        $chartData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Grafik Laporan Izin',
                'text' => 'Total Hari Izin',
                'data' => $data,
                'backgroundColor' => array_map(fn() => $this->getRandomRGBA(), $data),
            ]],
        ];
        
        $aiInsight = null;
        if ($request->has('generate_ai')) {
            // [FIX] Panggil AI dengan SEMUA data dan nama fungsi yang sesuai
            $aiInsight = $this->generateLeaveInsight($allLeaveReports, $chartData);
        }
            
        return view('hrga.laporanizin', compact('laporanizins', 'chartData', 'aiInsight'));
    }

    /**
     * [FIX] Nama fungsi dan parameter diubah agar sesuai konteks Laporan Izin.
     */
    private function generateLeaveInsight($leaveData, $chartData): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        if (!$apiKey || !$apiUrl) { /* ... error handling ... */ }
        if ($leaveData->isEmpty()) {
            return 'Tidak ada data izin yang cukup untuk dianalisis.';
        }

        try {
            // [FIX] Menggunakan nama kolom dan variabel yang sesuai
            $analysisData = [
                'periods_and_employees' => $chartData['labels'],
                'leave_values'        => $chartData['datasets'][0]['data'],
                'total_leave_days'    => $leaveData->sum('total_izin'),    // Menggunakan 'total_izin'
                'average_leave_days'  => $leaveData->avg('total_izin'),     // Menggunakan 'total_izin'
                'max_leave_days'      => $leaveData->max('total_izin'),      // Menggunakan 'total_izin'
                'data_count'          => $leaveData->count(),
                // [BARU] Menambahkan data agregat per karyawan untuk analisis yang lebih baik
                'leave_days_per_employee' => $leaveData->groupBy('nama')->map->sum('total_izin')->all(),
            ];
            
            // [FIX] Panggil fungsi prompt yang baru
            $prompt = $this->createLeaveAnalysisPrompt($analysisData);

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$apiUrl}?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [ 'temperature' => 0.7, 'maxOutputTokens' => 800 ],
                ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text', 'Tidak dapat menghasilkan insight dari AI.');
            }

            Log::error('Gemini API error: ' . $response->body());
            return 'Gagal menghubungi layanan analisis AI.';
        } catch (\Exception $e) {
            Log::error('Error generating AI insight: ' . $e->getMessage());
            return 'Terjadi kesalahan dalam menghasilkan analisis.';
        }
    }

    /**
     * [FIX] Seluruh prompt dirombak agar sesuai konteks Laporan Izin (HR).
     */
    private function createLeaveAnalysisPrompt(array $data): string
    {
        $total_leave_days = number_format($data['total_leave_days'], 0, ',', '.');
        $average_leave_days = number_format($data['average_leave_days'], 2, ',', '.');
        
        $employeeValuesStr = '';
        // Urutkan dari yang paling sering izin
        arsort($data['leave_days_per_employee']);
        foreach ($data['leave_days_per_employee'] as $employee => $total) {
            $employeeValuesStr .= "- **{$employee}**: " . number_format($total, 0, ',', '.') . " hari\n";
        }

        return <<<PROMPT
            Anda adalah seorang Manajer Sumber Daya Manusia (HR Manager) yang bertugas mengelola kehadiran dan produktivitas tim.

            Berikut adalah data rekapitulasi jumlah hari izin (di luar sakit dan cuti tahunan) yang diambil oleh karyawan dalam periode waktu tertentu.
            - Total Hari Izin Seluruh Karyawan: {$total_leave_days} hari
            - Rata-rata Hari Izin per Entri Laporan: {$average_leave_days} hari
            - Jumlah Total Laporan Izin: {$data['data_count']}

            **Akumulasi Total Hari Izin per Karyawan (Diurutkan dari Terbanyak):**
            {$employeeValuesStr}

            **Tugas Anda:**
            Buat laporan analisis singkat (maksimal 5 paragraf) dalam Bahasa Indonesia yang formal dan objektif untuk manajemen.

            Analisis harus fokus pada pola absensi izin dan dampaknya terhadap perencanaan operasional.
            1.  **Analisis Tren Pengambilan Izin:** Jelaskan tren umum dari pengajuan izin. Apakah ada bulan-bulan tertentu dengan frekuensi izin yang tinggi? Berikan hipotesis (misal: "Lonjakan izin di bulan Juni dan Desember mungkin bertepatan dengan musim liburan sekolah.").
            2.  **Identifikasi Pola pada Karyawan:** Tanpa menghakimi, identifikasi karyawan dengan jumlah izin yang signifikan di atas rata-rata. Apakah ini terkonsentrasi pada individu tertentu? Ini bisa menjadi dasar untuk diskusi personal mengenai keseimbangan kerja-hidup (work-life balance).
            3.  **Rekomendasi Perencanaan & Kebijakan:** Berikan 2-3 poin rekomendasi konkret. Contoh: 'Melihat tren izin yang tinggi di akhir tahun, manajer departemen harus proaktif menyusun jadwal kerja dan personel cadangan untuk menjaga kelancaran operasional.' atau 'Lakukan sosialisasi ulang mengenai kebijakan dan prosedur pengajuan izin untuk memastikan transparansi dan keadilan.'
            4.  **Dampak pada Alur Kerja Tim:** Berikan komentar singkat mengenai bagaimana pola izin ini dapat mempengaruhi pembagian beban kerja dan jadwal proyek. Sarankan langkah antisipasi, seperti program 'knowledge sharing' agar pekerjaan tidak terlalu bergantung pada satu orang.

            Gunakan format markdown untuk poin-poin agar mudah dibaca.
            PROMPT;
    }
    private function getRandomRGBA($opacity = 0.7)
    {
        return sprintf('rgba(%d, %d, %d, %.1f)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), $opacity);
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_izin' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            // Cek kombinasi unik date dan nama
            $exists = LaporanIzin::where('tanggal', $validatedData['tanggal'])
            ->where('nama', $validatedData['nama'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanIzin::create($validatedData);
    
            return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error Storing Data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'total_izin' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik date dan perusahaan
            $exists = LaporanIzin::where('nama', $validatedData['nama'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanIzin::create($validatedData);
    
            return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Izin: ' . $e->getMessage());
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanIzin $laporanizin)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'total_izin' => 'required|integer|min:0',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            
            // Cek kombinasi unik date dan nama
            $exists = LaporanIzin::where('nama', $validatedData['nama'])
                ->where('id_izin', '!=', $laporanizin->id_izin)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $laporanizin->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('laporanizin.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Laporan Izin: ' . $e->getMessage());
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            $mpdf = new Mpdf([
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
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Permission/Leave Report|');
            
            $htmlContent = "
            <div style='gap: 100px; width: 100%;'>
                <div style='width: 30%; float: left; padding-right: 20px;'>
                    <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Table Data</h2>
                    <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='border: 1px solid #000; padding: 1px;'>Date</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Employee</th>
                                <th style='border: 1px solid #000; padding: 2px;'>Permission Leave Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$tableHTML}
                        </tbody>
                    </table>
                </div>
                <div style='width: 65%; text-align:center; margin-left: 20px;'>
                    <h2 style='font-size: 14px; margin-bottom: 10px;'>Permission/Leave Chart</h2>
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

    public function destroy(LaporanIzin $laporanizin)
    {
        try {
            $laporanizin->delete();
            return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data: ' . $e->getMessage());
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function getRekapPenjualaPerusahaannData()
    {
        $data = LaporanIzin::all(['tanggal','nama','total_izin']);
    
        return response()->json($data);
    }

    public function showChart(Request $request)    
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = LaporanIzin::query();
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
        
        $laporanizins = $query
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
            ->get();
                
        // Siapkan data untuk chart
        $labels = $laporanizins->pluck('nama')->toArray();
        $data = $laporanizins->pluck('total_izin')->toArray();
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA(), $data);
    
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Sakit',
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

    public function chartTotal(Request $request)
    {
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        // Ambil data dari database
        $query = LaporanIzin::query();
        
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
        
        $laporanizins = $query->get();
    
        // Akumulasi total_sakit berdasarkan bulan
        $akumulasiData = [];
        foreach ($laporanizins as $item) {
            $bulan = \Carbon\Carbon::parse($item->tanggal)->format('F Y');
            if (!isset($akumulasiData[$bulan])) {
                $akumulasiData[$bulan] = 0;
            } 
            $akumulasiData[$bulan] += $item->total_izin;
        }
    
        // Siapkan data untuk chart
        $labels = array_keys($akumulasiData);
        $data = array_values($akumulasiData);
        $backgroundColors = array_map(fn() => $this->getRandomRGBAA1(), $data);
    
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Izin per Bulan',
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

