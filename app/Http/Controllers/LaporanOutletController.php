<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanOutlet;
use Illuminate\Support\Facades\Log;

class LaporanOutletController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanoutlets = LaporanOutlet::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);

        return view('procurements.laporanoutlet', compact('laporanoutlets'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pembelian' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanOutlet::where('bulan', $validatedata['bulan'])->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            LaporanOutlet::create($validatedata);
    
            return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Outlet data: ' . $e->getMessage());
            return redirect()->route('laporanoutlet.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanOutlet $laporanoutlet)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'total_pembelian' => 'required|integer|min:0'
            ]);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanOutlet::where('bulan', $validatedata['bulan'])->exists();
                
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $laporanoutlet->update($validatedata);
    
            return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Outlet data: ' . $e->getMessage());
            return redirect()->route('laporanoutlet.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanOutlet $laporanoutlet)
    {
        $laporanoutlet->delete();

        return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Dihapus');
    }
}
