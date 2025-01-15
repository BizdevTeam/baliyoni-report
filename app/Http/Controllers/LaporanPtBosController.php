<?php

namespace App\Http\Controllers;

use App\Models\LaporanPtBos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanPtBosController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanptboss = LaporanPtBos::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        
        return view('hrga.laporanptbos', compact('laporanptboss'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pekerjaan' => 'required|string',
                'kondisi_bulanlalu' => 'required|string',
                'kondisi_bulanini' => 'required|string',
                'update' => 'required|string',
                'rencana_implementasi' => 'required|string',
                'keterangan' => 'required|string'
            ]);
    
            LaporanPtBos::create($validatedata);
    
            return redirect()->route('laporanptbos.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing PT BOS Data: ' . $e->getMessage());
            return redirect()->route('laporanptbos.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanPtBos $laporanptbo)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pekerjaan' => 'required|string',
                'kondisi_bulanlalu' => 'required|string',
                'kondisi_bulanini' => 'required|string',
                'update' => 'required|string',
                'rencana_implementasi' => 'required|string',
                'keterangan' => 'required|string'
            ]);
    
            $laporanptbo->update($validatedata);
    
            return redirect()->route('laporanptbos.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating PT BOS Data: ' . $e->getMessage());
            return redirect()->route('laporanptbos.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(LaporanPtBos $laporanptbo)
    {
        $laporanptbo->delete();

        return redirect()->route('laporanptbos.index')->with('success', 'Data Berhaisil Dihapus');
    }
}
