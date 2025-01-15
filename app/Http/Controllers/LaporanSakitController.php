<?php

namespace App\Http\Controllers;

use App\Models\LaporanSakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanSakitController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporansakits = LaporanSakit::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporansakit', compact('laporansakits'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_sakit' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanSakit::where('nama', $validatedata['nama'])->exists();
                
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanSakit::create($validatedata);
    
            return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Rasio data: ' . $e->getMessage());
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSakit $laporansakit)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_sakit' => 'required|integer',
                'nama' => 'required|string'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanSakit::where('nama', $validatedata['nama'])->exists();
                
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporansakit->update($validatedata);
    
            return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Rasio data: ' . $e->getMessage());
            return redirect()->route('laporansakit.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanSakit $laporansakit)
    {
        $laporansakit->delete();

        return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Dihapus');
    }
}
