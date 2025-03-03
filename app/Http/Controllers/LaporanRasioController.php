<?php

namespace App\Http\Controllers;

use App\Models\LaporanRasio;
use App\Traits\DateValidationTraitAccSPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanRasioController extends Controller
{
    use DateValidationTraitAccSPI;
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanrasios = LaporanRasio::query()
        ->when($search, function($query, $search) {
            return $query->where('tanggal', 'like', "%$search%")
                         ->orWhere('keterangan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
        ->paginate($perPage);

            // Ubah path gambar agar dapat diakses dari frontend
            $laporanrasios->getCollection()->transform(function ($item) {
                $item->gambar_url = !empty($item->gambar) && file_exists(public_path("images/accounting/rasio/{$item->gambar}"))
                    ? asset("images/accounting/rasio/{$item->gambar}")
                    : asset("images/no-image.png"); // Placeholder jika tidak ada gambar
        
                return $item;
            });
        
            if ($request->ajax()) {
                return response()->json(['laporanrasios' => $laporanrasios]);
            }

        return view('accounting.rasio', compact('laporanrasios'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => 'mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string|max:255'
            ]);
            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
    
            if ($request->hasFile('file_excel')) {
                $filename = time() . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/rasio'), $filename);
                $validatedData['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/rasio'), $excelfilename);
                $validatedData['gambar'] = $excelfilename;
            }
    
            LaporanRasio::create($validatedData);
    
            return redirect()->route('rasio.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanRasio $rasio)
    {
        try {
            $fileRules = $rasio->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'mimes:xlsx,xls|max:2048';
            $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
            'file_excel' => $fileRules,
            'keterangan' => 'required|string|max:255'
        ]);
        $errorMessage = '';
        if (!$this->isInputAllowed($validatedData['tanggal'], $errorMessage)) {
            return redirect()->back()->with('error', $errorMessage);
        }

        if ($request->hasFile('gambar')) {
            $destinationimages = "images/accounting/rasio/" . $rasio->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }

            $filename = time() . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('images/accounting/rasio'), $filename);
            $validatedData['gambar'] = $filename;
        }

        if ($request->hasFile('file_excel')) {
            $destinationfiles = "files/accounting/rasio/" . $rasio->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

            $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
            $request->file('file_excel')->move(public_path('files/accounting/rasio'), $excelfilename);
            $validatedData['file_excel'] = $excelfilename;
        }

        $rasio->update($validatedData);

        return redirect()->route('rasio.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanRasio $rasio)
    {
        try {
            $destinationimages = "images/accounting/rasio/" . $rasio->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/rasio/" . $rasio->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $rasio->delete();
        
        return redirect()->route('rasio.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
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
        $laporans = LaporanRasio::where('tanggal', $validatedData['tanggal'])->get();
            
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

        // Tambahkan gambar sebagai header tanpa margin
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan Accounting -Laporan Rasio|');

        // Loop melalui setiap laporan dan tambahkan ke PDF
        foreach ($laporans as $index => $laporan) {
            $imageHTML = '';

            if (!empty($laporan->gambar) && file_exists(public_path("images/accounting/rasio/{$laporan->gambar}"))) {
                $imagePath = public_path("images/accounting/rasio/{$laporan->gambar}");
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
            return response($mpdf->Output("Laporan_Rasio_{$laporan->date}.pdf", 'D'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Laba_Rugi.pdf"');
    
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    
}
