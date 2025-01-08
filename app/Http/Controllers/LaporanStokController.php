<?php

namespace App\Http\Controllers;

use App\Models\LaporanStok;
use Illuminate\Http\Request;

class LaporanStokController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanstoks = LaporanStok::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        ;

        return view('procurements.laporanstok', compact('laporanstoks'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'stok' => 'required|integer|min:0'
        ]);

        LaporanStok::create($validatedata);

        return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, LaporanStok $laporanstok)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'stok' => 'required|integer|min:0'
        ]);

        $laporanstok->update($validatedata);

        return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanStok $laporanstok)
    {
        $laporanstok->delete();

        return redirect()->route('laporanstok.index')->with('success', 'Data Berhasil Dihpaus');
    }
}
