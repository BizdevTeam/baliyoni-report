<?php

namespace App\Http\Controllers;

use App\Models\ItBizdevBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\DateValidationTrait;


class ItBizdevBulananController extends Controller
{
    use DateValidationTrait;

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
            $validateData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'judul' => 'required|string|max:255'
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validateData['bulan'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            // Cek kombinasi unik bulan dan perusahaan
            $exists = ItBizdevBulanan::where('bulan', $validateData['bulan'])->exists();
    
            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            ItBizdevBulanan::create($validateData);
    
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
            $validateData = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'judul' => 'required|string|max:255'
            ]);

            $exists = ItBizdevBulanan::where('bulan', $validateData['bulan'])
                ->where('id_bizdevbulanan', '!=', $bizdevbulanan->id_bizdevbulanan)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            $bizdevbulanan->update($validateData);
    
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
