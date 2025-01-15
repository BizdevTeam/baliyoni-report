<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanHolding;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'perusahaan' => [
                    'required',
                    Rule::in([
                        'PT. Baliyoni Saguna',
                        'CV. ELKA MANDIRI',
                        'PT. NABA TECHNOLOGY SOLUTIONS',
                        'CV. BHIRMA TEKNIK',
                        'T. DWI SRIKANDI NUSANTARA']),
                ],
                'nilai' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanHolding::where('bulan', $validatedata['bulan'])
            ->where('perusahaan', $validatedata['perusahaan'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
        
            LaporanHolding::create($validatedata);
    
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Holdings: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanHolding $laporanholding)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'perusahaan' => [
                    'required',
                    Rule::in([
                        'PT. Baliyoni Saguna',
                        'CV. ELKA MANDIRI',
                        'PT. NABA TECHNOLOGY SOLUTIONS',
                        'CV. BHIRMA TEKNIK',
                        'PT. DWI SRIKANDI NUSANTARA']),
                ],
                'nilai' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = LaporanHolding::where('bulan', $validatedata['bulan'])
            ->where('perusahaan', $validatedata['perusahaan'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
            
            $laporanholding->update($validatedata);
    
            return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Holdings: ' . $e->getMessage());
            return redirect()->route('laporanholding.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanHolding $laporanholding)
    {
        $laporanholding->delete();

        return redirect()->route('laporanholding.index')->with('success', 'Data Berhasil Dihapus');
    }
}
