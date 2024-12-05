<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LaporanSPI;

class LaporanSPIController extends Controller
{
    public function index()
    {
        $laporans = LaporanSPI::all();
        return view('spi.laporanspi', compact('laporans'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'bulan_tahun' => 'required',
                'judul' => 'required',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi'=> 'required',
                'implementasi'=> 'required',
            ]);

            LaporanSPI::create($validatedData);
            return redirect()->route('laporanspi.index')->with('success', 'Laporan SPI berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->route('laporanspi.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSPI $laporanspi)
    {
        try {
            $validatedData = $request->validate([
                'bulan_tahun' => 'required',
                'judul' => 'required',
                'aspek' => 'required',
                'masalah' => 'required',
                'solusi'=> 'required',
                'implementasi'=> 'required',
            ]);

            $laporanspi->update($validatedData);
            return redirect()->route('laporanspi.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error( 'Kesalahan laporanspi update data' . $e->getMessage());
            return redirect()->route('laporanspi.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(LaporanSPI $laporanspi)
    {
        try {
            $laporanspi->delete();
            return redirect()->route('laporanspi.index')->with('success','Data berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('laporanspi.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
