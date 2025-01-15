<?php

namespace App\Http\Controllers;

use App\Models\LaporanTerlambat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanTerlambatController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporantelats = LaporanTerlambat::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporanterlambat', compact('laporantelats'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_telat' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanTerlambat::where('nama', $validatedata['nama'])->exists();
                        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanTerlambat::create($validatedata);
    
            return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Terlambat data: ' . $e->getMessage());
            return redirect()->route('laporantelat.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanTerlambat $laporantelat)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_telat' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanTerlambat::where('nama', $validatedata['nama'])->exists();
                            
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporantelat->update($validatedata);
    
            return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Laporan Terlambat data: ' . $e->getMessage());
            return redirect()->route('laporantelat.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanTerlambat $laporantelat)
    {
        $laporantelat->delete();

        return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Dihapus');
    }
}
