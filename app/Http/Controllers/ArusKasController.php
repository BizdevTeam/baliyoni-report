<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use Illuminate\Http\Request;

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
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'kas_masuk' => 'required|integer|min:0',
            'kas_keluar' => 'required|integer|min:0'
        ]);

        ArusKas::create($validatedata);

        return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, ArusKas $aruska)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'kas_masuk' => 'required|integer|min:0',
            'kas_keluar' => 'required|integer|min:0'
        ]);

        $aruska->update($validatedata);

        return redirect()->route('aruskas.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(ArusKas $aruska)
    {
        $aruska->delete();
    }
}
