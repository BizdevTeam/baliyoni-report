<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use Illuminate\Support\Facades\Log;

class LaporanIjasaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);
        $search = $request->input('search');

        $laporanijasas = LaporanIjasa::query()
        ->when($search, function ($query, $search) {
            return $query->where('tanggal', 'LIKE', "%$search%")
                         ->orWhere('permasalahan', 'LIKE', "%$search%");
        })
        ->orderBy('tanggal', 'DESC')
        ->paginate($perPage);
    
    return view('hrga.laporanijasa', compact('laporanijasas'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|date_format:H:i:s',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i:s'
            ]);
    
            LaporanIjasa::create($validatedata);
    
            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanIjasa $laporanijasa)
    {
        try {
            $validatedata = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'nullable|date_format:H:i:s',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i:s'
            ]);
    
            $laporanijasa->update($validatedata);
    
            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(LaporanIjasa $laporanijasa)
    {
        $laporanijasa->delete();

        return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Dihapus');
    }
}
