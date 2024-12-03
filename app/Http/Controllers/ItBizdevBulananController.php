<?php

namespace App\Http\Controllers;

use App\Models\ItBizdevBulanan;
use Illuminate\Http\Request;

class ItBizdevBulananController extends Controller
{
    // Menampilkan semua data
    public function index()
    {
        $itbizdevbulanans = ItBizdevBulanan::all();
        return view('it.bizdevbulanan', compact('itbizdevbulanans'));
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'judul' => 'required|string|max:255'
        ]);

        ItBizdevBulanan::create($validatedata);

        return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    // Mengupdate data
    public function update(Request $request, ItBizdevBulanan $bizdevbulanan)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'judul' => 'required|string|max:255'
        ]);

        $bizdevbulanan->update($validatedata);

        return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Diupdate');
    }

    // Menghapus data
    public function destroy(ItBizdevBulanan $bizdevbulanan)
    {
        $bizdevbulanan->delete();

        return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Dihapus');
    }
}
