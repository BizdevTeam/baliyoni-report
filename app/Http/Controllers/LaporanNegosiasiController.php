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
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_negosiasi' => 'required|integer|min:0'
        ]);

        LaporanNegosiasi::create($validatedata);

        return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhasil Ditambah');
    }

    public function update(Request $request, LaporanNegosiasi $laporannegosiasi)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_negosiasi' => 'required|integer|min:0'
        ]);

        $laporannegosiasi->update($validatedata);

        return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhsail Diupdate');
    }

    public function destroy(LaporanNegosiasi $laporannegosiasi)
    {
        $laporannegosiasi->delete();

        return redirect()->route('laporannegosiasi.index')->with('success', 'Data Berhsail Dihapus');
    }
}
