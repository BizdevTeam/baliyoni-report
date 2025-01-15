<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanTaxPlaning;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanTaxPlaningController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporantaxplanings = LaporanTaxPlaning::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan', 'like', "%$search%")
                         ->orWhere('keterangan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);
        return view('accounting.taxplaning', compact('laporantaxplanings'));
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
                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedata['file_excel'] = $excelfilename;
            }
    
            if ($request->hasFile('gambar')) {
                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedata['gambar'] = $filename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanTaxPlaning::where('bulan', $validatedata['bulan'])->exists();
                    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanTaxPlaning::create($validatedata);
    
            return redirect()->route('taxplaning.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing taxplaning data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanTaxPlaning $taxplaning)
    {
        try {
            $fileRules = $taxplaning->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'required|mimes:xlsx,xls|max:2048';
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
                'file_excel' => $fileRules,
                'keterangan' => 'required|string|max:255'
            ]);

            if ($request->hasFile('gambar')) {
                $destinationimages = "images/accounting/taxplaning/" . $taxplaning->gambar;
                if (File::exists($destinationimages)) {
                    File::delete($destinationimages);
                }

                $filename = date('d-m-Y') . '_' . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/taxplaning'), $filename);
                $validatedata['gambar'] = $filename;
            }

            if ($request->hasFile('file_excel')) {
                $destinationfiles = "files/accounting/taxplaning/" . $taxplaning->file_excel;
                if (File::exists($destinationfiles)) {
                    File::delete($destinationfiles);
                }

                $excelfilename = date('d-m-Y') . '_' . $request->file('file_excel')->getClientOriginalName();
                $request->file('file_excel')->move(public_path('files/accounting/taxplaning'), $excelfilename);
                $validatedata['file_excel'] = $excelfilename;
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanTaxPlaning::where('bulan', $validatedata['bulan'])
            ->where('id_taxplaning', '!=', $taxplaning->id_taxplaning)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }

            $taxplaning->update($validatedata);

            return redirect()->route('taxplaning.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating taxplaning data: ' . $e->getMessage());
            return redirect()->route('taxplaning.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanTaxPlaning $taxplaning)
    {
        try {
            $destinationimages = "images/accounting/taxplaning/" . $taxplaning->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/taxplaning/" . $taxplaning->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $taxplaning->delete();
        
        return redirect()->route('taxplaning.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting taxplaning data: ' . $e->getMessage());
            return redirect()->route('taxplaning.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
