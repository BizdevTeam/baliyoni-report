<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanNegosiasi;
use App\Models\LaporanNeraca;
use Illuminate\Support\Facades\Log;

class LaporanNegosiasiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_pages', 12);
            $search = $request->input('search');
    
            $laporannegosiasis = LaporanNegosiasi::query()
                ->when($search, function ($query, $search) {
                    return $query->where('bulan', 'Like', "%$search%");
                })
                ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
                ->paginate($perPage);
    
            return view('procurements.laporannegosiasi', compact('laporannegosiasis'));
        } catch (\Exception $e) {
            Log::error('Error Negosiasi: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_negosiasi' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanNegosiasi::where('bulan', $validatedata['bulan'])->exists();
        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanNegosiasi::create($validatedata);
    
            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            Log::error('Error Storing Negosiasi data: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanNegosiasi $laporannegosiasi)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_negosiasi' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanNegosiasi::where('bulan', $validatedata['bulan'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporannegosiasi->update($validatedata);
    
            return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhsail Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Negosiasi data: ' . $e->getMessage());
            return redirect()->route('laporannegosiasi.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanNegosiasi $laporannegosiasi)
    {
        $laporannegosiasi->delete();

        return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhsail Dihapus');
    }
}
