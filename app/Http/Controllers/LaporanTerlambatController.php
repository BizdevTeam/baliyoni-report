<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanTerlambat;
use Illuminate\Support\Facades\Log;

class LaporanTerlambatController extends Controller
{
    public function index()
    {
        return view('hrga.laporanterlambat');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'total_terlambat' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            $laporanTerlambat = new laporanTerlambat();
            $laporanTerlambat->bulan_tahun = $validated['bulan_tahun'];
            $laporanTerlambat->total_terlambat = $validated['total_terlambat'];
            $laporanTerlambat->nama = $validated['nama'] ?? null;
            $laporanTerlambat->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data. Error: ' . $e->getMessage()
            ]);
        }
    }

    // Mengupdate data laporan sakit
    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'total_terlambat' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            $laporanTerlambat = laporanTerlambat::findOrFail($id);
            $laporanTerlambat->bulan_tahun = $validated['bulan_tahun'];
            $laporanTerlambat->total_terlambat = $validated['total_terlambat'];
            $laporanTerlambat->nama = $validated['nama'] ?? null;
            $laporanTerlambat->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data. Error: ' . $e->getMessage()
            ]);
        }
    }

    // Menghapus data laporan sakit
    public function destroy($id)
    {
        try {
            $laporanTerlambat = laporanTerlambat::findOrFail($id);
            $laporanTerlambat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data. Error: ' . $e->getMessage()
            ]);
        }
    }

    // Mengambil data laporan sakit dengan filter bulan/tahun
    public function getData(Request $request)
    {
        try {
            $filter = $request->input('bulan_tahun', '');
            $query = laporanTerlambat::query();

            if ($filter) {
                $query->where('bulan_tahun', $filter);
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data. Error: ' . $e->getMessage()
            ]);
        }
    }
}
