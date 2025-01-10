<?php

namespace App\Http\Controllers;

use App\Models\LaporanPtBos;
use Illuminate\Http\Request;

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
    }

    public function update(Request $request, LaporanPtBos $laporanptbo)
    {
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
    }

    public function destroy(LaporanPtBos $laporanptbo)
    {
        $laporanptbo->delete();

        return redirect()->route('laporanptbos.index')->with('success', 'Data Berhaisil Dihapus');
    }
}
