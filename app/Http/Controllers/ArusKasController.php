<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArusKasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $aruskass = ArusKas::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);

        return view('accounting.aruskas', compact('aruskass'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ArusKas::where('bulan', $validatedata['bulan'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            ArusKas::create($validatedata);

            return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error updating Arus Kas: ' . $e->getMessage());
            return redirect()->route('aruskas.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ArusKas $aruska)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas_masuk' => 'required|integer|min:0',
                'kas_keluar' => 'required|integer|min:0'
            ]);
    
            $aruska->update($validatedata);
    
            // Cek kombinasi unik bulan dan perusahaan
            $exists = ArusKas::where('bulan', $validatedata['bulan'])->exists();
        
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            // Logging untuk debug
            Log::error('Error updating Arus Kas: ' . $e->getMessage());
            return redirect()->route('aruskas.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(ArusKas $aruska)
    {
        $aruska->delete();

        return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Dihapus');
    }
}
