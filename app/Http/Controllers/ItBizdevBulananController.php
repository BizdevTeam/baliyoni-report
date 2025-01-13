<?php

namespace App\Http\Controllers;

use App\Models\ItBizdevBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItBizdevBulananController extends Controller
{
    // Menampilkan semua data
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $itbizdevbulanans = ItBizdevBulanan::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);
            
        return view('it.bizdevbulanan', compact('itbizdevbulanans'));
    }

    // Menyimpan data baru
    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'judul' => 'required|string|max:255'
            ]);
    
            ItBizdevBulanan::create($validatedata);
    
            return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing bizdevbulanan data: ' . $e->getMessage());
            return redirect()->route('bizdevbulanan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    // Mengupdate data
    public function update(Request $request, ItBizdevBulanan $bizdevbulanan)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'judul' => 'required|string|max:255'
            ]);
    
            $bizdevbulanan->update($validatedata);
    
            return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating bizdevbulanan data: ' . $e->getMessage());
            return redirect()->route('bizdevbulanan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    // Menghapus data
    public function destroy(ItBizdevBulanan $bizdevbulanan)
    {
        try {
            $bizdevbulanan->delete();

        return redirect()->route('bizdevbulanan.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Erorr deleting bizdevbulanan data: ' . $e->getMessage());
            return redirect()->route('bizdevbulanan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
