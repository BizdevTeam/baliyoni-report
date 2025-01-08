<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHolding;
use Illuminate\Support\Facades\Log;

class LaporanHoldingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_pages', 12);
            $search = $request->input('search');

            $laporanpembelianholdings = LaporanHolding::query()
                ->when($search, function ($query, $search) {
                    return $query->where('bulan', 'Like', "%$search%");
                })
                ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
                ->paginate($perPage);

            return view('procurements.laporanholding', compact('laporanpembelianholdings'));
        } catch (\Exception $e) {
            Log::error('Error Holdings: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {

        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'perusahaan' => 'required|in:PT. Baliyoni Saguna,CV. ELKA MANDIRI, PT. NABA TECHNOLOGY SOLUTIONS,CV. BHIRMA TEKNIK,PT. DWI SRIKANDI NUSANTARA',
            'nilai' => 'required|integer|min:0'
        ]);
    
        LaporanHolding::create($validatedata);

        return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, LaporanHolding $laporanholding)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'perusahaan' => 'required|in:
                            PT. Baliyoni Saguna,
                            CV. ELKA MANDIRI,
                            PT. NABA TECHNOLOGY SOLUTIONS,
                            CV. BHIRMA TEKNIK,
                            PT. DWI SRIKANDI NUSANTARA',
            'nilai' => 'required|integer|min:0'
        ]);
        
        $laporanholding->update($validatedata);

        return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanHolding $laporanholding)
    {
        $laporanholding->delete();

        return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Dihapus');
    }
}
