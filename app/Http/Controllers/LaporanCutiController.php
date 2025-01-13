<?php

namespace App\Http\Controllers;

use App\Models\LaporanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanCutiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporancutis = LaporanCuti::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporancuti', compact('laporancutis'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_cuti' => 'required|integer',
            'nama' => 'required|string'
        ]);

        LaporanCuti::create($validatedata);

        return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Ditambah');
    }

    public function update(Request $request, LaporanCuti $laporancuti)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_cuti' => 'required|integer',
            'nama' => 'required|string'
        ]);

        $laporancuti->update($validatedata);

        return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanCuti $laporancuti)
    {
        $laporancuti->delete();

        return redirect()->route('laporancuti.index')->with('success', 'Data Berhasil Dihapus');
    }
}
