<?php

namespace App\Http\Controllers;

use App\Models\LaporanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanCutiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporancutis = LaporanCuti::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporancuti', compact('laporancutis'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_cuti' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanCuti::where('bulan', $validatedata['bulan'])->exists();
        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanCuti::create($validatedata);
    
            return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            Log::error('Error storing Laporan Coti data: ' . $e->getMessage());
            return redirect()->route('laporancuti.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanCuti $laporancuti)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_cuti' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanCuti::where('bulan', $validatedata['bulan'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporancuti->update($validatedata);
    
            return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error storing Laporan Coti data: ' . $e->getMessage());
            return redirect()->route('laporancuti.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
        
    }

    public function destroy(LaporanCuti $laporancuti)
    {
        $laporancuti->delete();

        return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Dihapus');
    }
}
