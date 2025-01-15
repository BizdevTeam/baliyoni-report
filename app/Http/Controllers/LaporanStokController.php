<?php

namespace App\Http\Controllers;

use App\Models\LaporanStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanStokController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanstoks = LaporanStok::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        ;

        return view('procurements.laporanstok', compact('laporanstoks'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'stok' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanStok::where('bulan', $validatedata['bulan'])->exists();
                    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanStok::create($validatedata);
    
            return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Stok data: ' . $e->getMessage());
            return redirect()->route('laporanstok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanStok $laporanstok)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'stok' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanStok::where('bulan', $validatedata['bulan'])->exists();
                        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporanstok->update($validatedata);
    
            return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Stok data: ' . $e->getMessage());
            return redirect()->route('laporanstok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanStok $laporanstok)
    {
        $laporanstok->delete();

        return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Dihpaus');
    }
}
