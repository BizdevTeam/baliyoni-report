<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class PerusahaanController extends Controller
{
    public function penjualanPerusahaan()
    {
        // Ambil semua data perusahaan dari database
        $perusahaans = Perusahaan::all();

        dd($perusahaans);

        // Kirim data ke tampilan rekappenjualanperusahaan
        return view('rekappenjualanperusahaan', compact('perusahaans'));
    }

    public function laporanHolding()
    {
        // Ambil semua data perusahaan dari database
        $perusahaans = Perusahaan::all();
        
        dd($perusahaans);

        // Kirim data ke tampilan laporanholding
        return view('laporanholding', compact('perusahaans'));
    }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $perusahaans = Perusahaan::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_perusahaan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(nama_perusahaan) DESC, MONTH(nama_perusahaan) ASC') // Urutkan berdasarkan tahun (descending) dan perusahaan (ascending)
            ->paginate($perPage);

        return view('marketings.perusahaan', compact('perusahaans'));    }
    
    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'nama_perusahaan' => 'required',

            ]);

            // Cek kombinasi unik nama_perusahaan dan perusahaan
            $exists = Perusahaan::where('nama_perusahaan', $validatedata['nama_perusahaan'])->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            Perusahaan::create($validatedata);
    
            return redirect()->route('perusahaan.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error Storing Rekap Penjualan Data: ' . $e->getMessage());
            return redirect()->route('perusahaan.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function update(Request $request, Perusahaan $perusahaan)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'nama_perusahaan' => 'required',

            ]);

            // Cek kombinasi unik nama_perusahaan dan perusahaan
            $exists = Perusahaan::where('nama_perusahaan', $validatedData['nama_perusahaan'])
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
    public function destroy(Perusahaan $perusahaan)
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

