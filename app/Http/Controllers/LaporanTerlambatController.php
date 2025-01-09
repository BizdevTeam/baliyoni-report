<?php

namespace App\Http\Controllers;

use App\Models\LaporanTerlambat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanTerlambatController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporantelats = LaporanTerlambat::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporanterlambat', compact('laporantelats'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_telat' => 'required|integer',
            'nama' => 'required|string'
        ]);

        LaporanTerlambat::create($validatedata);

        return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Ditambah');
    }

    public function update(Request $request, LaporanTerlambat $laporantelat)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_telat' => 'required|integer',
            'nama' => 'required|string'
        ]);

        $laporantelat->update($validatedata);

        return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanTerlambat $laporantelat)
    {
        $laporantelat->delete();

        return redirect()->route('laporantelat.index')->with('success', 'Data Berhasil Dihapus');
    }
}
