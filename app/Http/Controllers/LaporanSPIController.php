<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LaporanSPI;

class LaporanSPIController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporans = LaporanSPI::query()
        ->when($search, function($query, $search) {
            return $query->where('bulan_tahun', 'like', "%$search%")
                         ->orWhere('judul', 'like', "%$search%")
                         ->orWhere('aspek', 'like', "%$search%")
                         ->orWhere('masalah', 'like', "%$search%")
                         ->orWhere('solusi', 'like', "%$search%")
                         ->orWhere('implementasi', 'like', "%$search%");
        })
        ->orderByRaw('YEAR(bulan_tahun) DESC, MONTH(bulan_tahun) ASC')
        ->paginate($perPage);
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
