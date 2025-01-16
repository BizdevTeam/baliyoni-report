<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanLabaRugi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanLabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanlabarugis = LaporanLabaRugi::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);
        return view('accounting.labarugi', compact('laporanlabarugis'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => 'required|mimes:xlsx,xls|max:2048',
                'keterangan' => 'required|string|max:255'
            ]);
    
            if ($request->hasFile('file_excel')) {
                $filename = time() . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/labarugi'), $filename);
                $validatedata['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/labarugi'), $excelfilename);
                $validatedata['gambar'] = $excelfilename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanLabaRugi::where('bulan', $validatedata['bulan'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanLabaRugi::create($validatedata);
    
            return redirect()->route('labarugi.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing labarugi data: ' . $e->getMessage());
            return redirect()->route('labarugi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanLabaRugi $labarugi)
    {
        try {
            $fileRules = $labarugi->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'required|mimes:xlsx,xls|max:2048';
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
            'file_excel' => $fileRules,
            'keterangan' => 'required|string|max:255'
        ]);

        if ($request->hasFile('gambar')) {
            $destinationimages = "images/accounting/labarugi/" . $labarugi->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }

            $filename = time() . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('images/accounting/labarugi'), $filename);
            $validatedata['gambar'] = $filename;
        }

        if ($request->hasFile('file_excel')) {
            $destinationfiles = "files/accounting/labarugi/" . $labarugi->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

            $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
            $request->file('file_excel')->move(public_path('files/accounting/labarugi'), $excelfilename);
            $validatedata['file_excel'] = $excelfilename;
        }

        // Cek kombinasi unik bulan dan perusahaan
        $exists = LaporanLabaRugi::where('bulan', $validatedata['bulan'])
                ->where('id_labarugi', '!=', $labarugi->id_labarugi)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

        $labarugi->update($validatedata);

        return redirect()->route('labarugi.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating labarugi data: ' . $e->getMessage());
            return redirect()->route('labarugi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanLabaRugi $labarugi)
    {
        try {
            $destinationimages = "images/accounting/labarugi/" . $labarugi->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/labarugi/" . $labarugi->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $labarugi->delete();
        
        return redirect()->route('labarugi.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting labarugi data: ' . $e->getMessage());
            return redirect()->route('labarugi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
