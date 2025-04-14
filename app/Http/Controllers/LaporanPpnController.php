<?php

namespace App\Http\Controllers;

use App\Models\LaporanPpn;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class LaporanPpnController extends Controller
{
    use DateValidationTraitAccSPI;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanPpn::query();

        // Filter berdasarkan tanggal jika ada
        if ($search) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') LIKE ?", ["%$search%"]);
        }
    
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
            }
        }
        // Ambil data dengan pagination
        $laporanppns = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

              // Ubah path thumbnail agar dapat diakses dari frontend
        $laporanppns->getCollection()->transform(function ($item) {
            $item->gambar_url = !empty($item->thumbnail) && file_exists(public_path("images/accounting/ppn/{$item->thumbnail}"))
                ? asset("images/accounting/ppn/{$item->thumbnail}")
                : asset("images/no-image.png"); // Placeholder jika tidak ada thumbnail
        
                return $item;
            });
        
            if ($request->ajax()) {
                return response()->json(['laporanppns' => $laporanppns]);
            }

        return view('accounting.laporanppn', compact('laporanppns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('file')) {
                $excelFileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $excelFileName);
                $validatedData['file'] = $excelFileName;
            }

            if ($request->hasFile('thumbnail')) {
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedData['thumbnail'] = $fileName;
            }

            LaporanPpn::create($validatedData);
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaporanPpn $laporanppn)
    {
        try{
            $validatedData = $request->validate([
                'tanggal' => 'required|string',
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string',
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->hasFile('thumbnail')) {
                $destinationImages = "images/accounting/ppn/" . $laporanppn->thumbnail;
                if (File::exists($destinationImages)) {
                    File::delete($destinationImages);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('thumbnail')->getClientOriginalName();
                $request->file('thumbnail')->move(public_path('images/accounting/ppn'), $fileName);
                $validatedData['thumbnail'] = $fileName;
            }

            if ($request->hasFile('file')) {
                $destinationFile = "files/accounting/ppn/" . $laporanppn->file;
                if (File::exists($destinationFile)) {
                    File::delete($destinationFile);
                }
                $fileName = date('d-m-Y') . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move(public_path('files/accounting/ppn'), $fileName);
                $validatedData['file'] = $fileName;
            }

            $laporanppn->update($validatedData);
            
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil diubah!');

        } catch (\Exception $e) {
            Log::error('Error updating laporanppn: ' . $e->getMessage());
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanPpn $laporanppn)
    {
        try{            
            // Cek dan hapus file
            $destination = ('images/accounting/ppn/' . $laporanppn->thumbnail);
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $laporanppn->delete();
            return redirect()->route('laporanppn.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika terjadi error, redirect dengan pesan error
            return redirect()->route('laporanppn.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
    public function exportPDF(Request $request)
    {
        try {
            // Validasi input date
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
            ]);
    
            // Ambil data laporan berdasarkan date yang dipilih
            $laporans = LaporanPpn::where('tanggal', $validatedData['tanggal'])->get();
    
            if (!$laporans) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
    
            // Inisialisasi mPDF
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L', // Landscape orientation
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 35, // Tambahkan margin atas untuk header teks
                'margin_bottom' => 20, // Kurangi margin bawah
                'format' => 'A4', // Ukuran kertas A4
            ]);
    
            // Tambahkan thumbnail sebagai header tanpa margin
            $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
            $mpdf->SetHTMLHeader("
                <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                    <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
                </div>
            ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya
    
            // Tambahkan footer ke PDF
            $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting - Laporan PPN|');
    
           // Loop melalui setiap laporan dan tambahkan ke PDF
           foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->thumbnail) && file_exists(public_path("images/accounting/ppn/{$laporan->thumbnail}"))) {
                $imagePath = public_path("images/accounting/ppn/{$laporan->thumbnail}");
                $imageHTML = "<img src='{$imagePath}' style='width: auto; max-height: 500px; display: block; margin: auto;' />";
            } else {
                $imageHTML = "<p style='text-align: center; color: red; font-weight: bold;'>Gambar tidak tersedia</p>";
            }

            // Konten untuk setiap laporan
            $htmlContent = "
        <div style='text-align: center; top: 0; margin: 0; padding: 0;'>
            {$imageHTML}
                <h3 style='margin: 0; padding: 0;'>Keterangan : {$laporan->keterangan}</h3>
                <h3 style='margin: 0; padding: 0;'>Laporan : {$laporan->tanggal_formatted}</h3>
        </div>

            ";

            // Tambahkan ke PDF
            $mpdf->WriteHTML($htmlContent);
        }
    
            // Output PDF
            return response($mpdf->Output("Laporan_PPn_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_PPN.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

}
