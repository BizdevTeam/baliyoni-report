<?php

namespace App\Http\Controllers;

use App\Models\LaporanSPITI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class laporanSPITiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        $laporanspitis = LaporanSPITI::query()
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
        return view("spi.laporanspiti" , compact("laporanspitis"));
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

            LaporanSPITI::create($validatedData);
            return redirect()->route('laporanspiti.index')->with('success', 'Laporan SPITI berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanSPITI $laporanspiti)
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

            $laporanspiti->update($validatedData);
            return redirect()->route('laporanspiti.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error( 'Kesalahan laporanspi update data' . $e->getMessage());
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }


    }

    public function destroy(LaporanSPITI $laporanspiti)
    {
        try {
            $laporanspiti->delete();
            return redirect()->route('laporanspiti.index')->with('success','Data berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('laporanspiti.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
