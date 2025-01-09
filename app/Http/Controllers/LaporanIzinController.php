<?php

namespace App\Http\Controllers;

use App\Models\LaporanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanIzinController extends Controller
{
    // Menampilkan halaman utama
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanizins = LaporanIzin::query()
        ->when($search, function ($query, $search) {
            return $query->where('bulan', 'LIKE', "%$search%")
                         ->orWhere('nama', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
        ->paginate($perPage);

        return view('hrga.laporanizin', compact('laporanizins'));
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_izin' => 'required|integer',
            'nama' => 'required|string'
        ]);

        LaporanIzin::create($validatedata);

        return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, LaporanIzin $laporanizin)
    {
        $validatedata = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'total_izin' => 'required|integer',
            'nama' => 'required|string'
        ]);
        
        $laporanizin->update($validatedata);

        return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy(LaporanIzin $laporanizin)
    {
        $laporanizin->delete();

        return redirect()->route('laporanizin.index')->with('success', 'Data Berhasil Dihapus');
    }
}
