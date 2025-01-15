<?php

namespace App\Http\Controllers;

use App\Models\LaporanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanIzinController extends Controller
{
    // Menampilkan halaman utama
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanizins = LaporanIzin::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporanizin', compact('laporanizins'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_izin' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanIzin::where('nama', $validatedata['nama'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanIzin::create($validatedata);
    
            return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Laporan Izin: ' . $e->getMessage());
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanIzin $laporanizin)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_izin' => 'required|integer',
                'nama' => 'required|string'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanIzin::where('nama', $validatedata['nama'])->exists();
        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
            
            $laporanizin->update($validatedata);
    
            return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Laporan Izin: ' . $e->getMessage());
            return redirect()->route('laporanizin.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanIzin $laporanizin)
    {
        $laporanizin->delete();

        return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Dihapus');
    }
}
