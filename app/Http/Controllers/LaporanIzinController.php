<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIzin;
use Illuminate\Support\Facades\Log;

class LaporanIzinController extends Controller
{
    public function index()
    {
        return view('hrga.laporanizin');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'total_izin' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            $laporanIzin = new laporanIzin();
            $laporanIzin->bulan_tahun = $validated['bulan_tahun'];
            $laporanIzin->total_izin = $validated['total_izin'];
            $laporanIzin->nama = $validated['nama'] ?? null;
            $laporanIzin->save();

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
            'total_izin' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            $laporanIzin = laporanIzin::findOrFail($id);
            $laporanIzin->bulan_tahun = $validated['bulan_tahun'];
            $laporanIzin->total_izin = $validated['total_izin'];
            $laporanIzin->nama = $validated['nama'] ?? null;
            $laporanIzin->save();

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
            $laporanIzin = laporanIzin::findOrFail($id);
            $laporanIzin->delete();

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
            $query = laporanIzin::query();

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
