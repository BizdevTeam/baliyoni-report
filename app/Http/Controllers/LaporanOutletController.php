<?php

namespace App\Http\Controllers;

use App\Models\LaporanOutlet;
use Illuminate\Http\Request;

class LaporanOutletController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanoutlets = LaporanOutlet::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);

        return view('procurements.laporanoutlet', compact('laporanoutlets'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_pembelian' => 'required|integer|min:0'
        ]);

        LaporanOutlet::create($validatedata);

        return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, LaporanOutlet $laporanoutlet)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_pembelian' => 'required|integer|min:0'
        ]);

        $laporanoutlet->update($validatedata);

        return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanOutlet $laporanoutlet)
    {
        $laporanoutlet->delete();

        return redirect()->route('laporanoutlet.index')->with('success', 'Data Berhasil Dihapus');
    }
}
