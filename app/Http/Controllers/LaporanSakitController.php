<?php

namespace App\Http\Controllers;

use App\Models\LaporanSakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanSakitController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);
        $search = $request->input('search');

        $laporansakits = LaporanSakit::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporansakit', compact('laporansakits'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_sakit' => 'required|integer',
            'nama' => 'required|string'
        ]);

        LaporanSakit::create($validatedata);

        return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, LaporanSakit $laporansakit)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_sakit' => 'required|integer',
            'nama' => 'required|string'
        ]);

        $laporansakit->update($validatedata);

        return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanSakit $laporansakit)
    {
        $laporansakit->delete();

        return redirect()->route('laporansakit.index')->with('success', 'Data Berhasil Dihapus');
    }
}
