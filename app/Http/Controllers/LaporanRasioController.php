<?php

namespace App\Http\Controllers;

use App\Models\LaporanRasio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LaporanRasioController extends Controller
{
    public function index()
    {
        $laporanrasios = LaporanRasio::all();
        return view('accounting.rasio', compact('laporanrasios'));
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
                $request->file('file_excel')->move(public_path('files/accounting/rasio'), $filename);
                $validatedata['file_excel'] = $filename;
            }
    
            if ($request->hasFile('gambar')) {
                $excelfilename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/accounting/rasio'), $excelfilename);
                $validatedata['gambar'] = $excelfilename;
            }
    
            LaporanRasio::create($validatedata);
    
            return redirect()->route('rasio.index')->with('success', 'Data Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error storing rasio data: ' . $e->getMessage());
            return redirect()->route('rasio.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanRasio $rasio)
    {
        try {
            $fileRules = $rasio->file_excel ? 'nullable|mimes:xlsx,xls|max:2048' : 'required|mimes:xlsx,xls|max:2048';
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550',
            'file_excel' => $fileRules,
            'keterangan' => 'required|string|max:255'
        ]);

        if ($request->hasFile('gambar')) {
            $destinationimages = "images/accounting/rasio/" . $rasio->gambar;
            if (File::exists($destinationimages)) {
                File::delete($destinationimages);
            }

            $filename = time() . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('images/accounting/rasio'), $filename);
            $validatedata['gambar'] = $filename;
        }

        if ($request->hasFile('file_excel')) {
            $destinationfiles = "files/accounting/rasio/" . $rasio->file_excel;
            if (File::exists($destinationfiles)) {
                File::delete($destinationfiles);
            }

            $excelfilename = time() . $request->file('file_excel')->getClientOriginalName();
            $request->file('file_excel')->move(public_path('files/accounting/rasio'), $excelfilename);
            $validatedata['file_excel'] = $excelfilename;
        }

        $rasio->update($validatedata);

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
}
