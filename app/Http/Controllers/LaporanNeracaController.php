<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNeraca;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanNeracaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanneracas = LaporanNeraca::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan', 'like', "%$search%")
                         ->orWhere('keterangan', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);
        return view('accounting.neraca', compact('laporanneracas'));
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
                $request->file('file_excel')->move(public_path('files/accounting/neraca'), $filename);
                $validatedata['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/neraca'), $excelfilename);
                $validatedata['gambar'] = $excelfilename;
            }
    
            LaporanNeraca::create($validatedata);
    
            return redirect()->route('neraca.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanNeraca $neraca)
    {
        try {
            $fileRules = $neraca->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'required|mimes:xlsx,xls|max:2048';
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
            'file_excel' => $fileRules,
            'keterangan' => 'required|string|max:255'
        ]);

        if ($request->hasFile('gambar')) {
            $destinationimages = "images/accounting/neraca/" . $neraca->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }

            $filename = time() . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('images/accounting/neraca'), $filename);
            $validatedata['gambar'] = $filename;
        }

        if ($request->hasFile('file_excel')) {
            $destinationfiles = "files/accounting/neraca/" . $neraca->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

            $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
            $request->file('file_excel')->move(public_path('files/accounting/neraca'), $excelfilename);
            $validatedata['file_excel'] = $excelfilename;
        }

        $neraca->update($validatedata);

        return redirect()->route('neraca.index')->with('success', 'Data Telah Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanNeraca $neraca)
    {
        try {
            $destinationimages = "images/accounting/neraca/" . $neraca->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }
        
        $destinationfiles = "files/accounting/neraca/" . $neraca->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

        $neraca->delete();
        
        return redirect()->route('neraca.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting neraca data: ' . $e->getMessage());
            return redirect()->route('neraca.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
