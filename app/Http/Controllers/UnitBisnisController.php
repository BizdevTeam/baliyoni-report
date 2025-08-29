<?php

namespace App\Http\Controllers;

use App\Models\UnitBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UnitBisnisController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $unit_bisnis = UnitBisnis::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_unit', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(nama_unit) DESC, MONTH(nama_unit) ASC') // Urutkan berdasarkan tahun (descending) dan perusahaan (ascending)
            ->paginate($perPage);

        return view('marketings.unit', compact('unit_bisnis'));    }
    
    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'nama_unit' => 'required',

            ]);

            // Cek kombinasi unik nama_unit dan perusahaan
            $exists = UnitBisnis::where('nama_unit', $validatedata['nama_unit'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            UnitBisnis::create($validatedata);
    
            return redirect()->route('perusahaan.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('perusahaan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function update(Request $request, UnitBisnis $perusahaan)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'nama_unit' => 'required',

            ]);

            // Cek kombinasi unik nama_unit dan perusahaan
            $exists = UnitBisnis::where('nama_unit', $validatedData['nama_unit'])
                ->where('id_rp', '!=', $perusahaan->id_rp)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'it cannot be changed, the data already exists.');
            }
    
            // Update data
            $perusahaan->update($validatedData);
    
            // Redirect dengan pesan sukses
            return redirect()->route('perusahaan.index')->with('success', 'Data berhasil diperbarui.');
        } catch (ValidationException $e) {
            // Tangani error validasi
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum dan log untuk debugging
            Log::error('Error updating Rekap Penjualan: ' . $e->getMessage());
            return redirect()
                ->route('perusahaan.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function destroy(UnitBisnis $perusahaan)
    {
        try {
            $perusahaan->delete();
            return redirect()->route('perusahaan.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error Deleting Data Data: ' . $e->getMessage());
            return redirect()->route('perusahaan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
