<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanCuti;


class LaporanCutiController extends Controller
{
    public function index()
    {
        return view('hrga.laporancuti');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'total_cuti' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            // Cek duplikasi nama pada bulan dan tahun yang sama
            $existingRecord = LaporanCuti::where('nama', $validated['nama'])
                ->where('bulan_tahun', $validated['bulan_tahun'])
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama sudah terdaftar pada bulan dan tahun yang sama.',
                ]);
            }

            $laporanCuti = new LaporanCuti();
            $laporanCuti->bulan_tahun = $validated['bulan_tahun'];
            $laporanCuti->total_cuti = $validated['total_cuti'];
            $laporanCuti->nama = $validated['nama'] ?? null;
            $laporanCuti->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data. Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'total_cuti' => 'required|numeric|min:0',
            'nama' => 'nullable|string|max:255',
        ]);

        try {
            $laporanCuti = LaporanCuti::findOrFail($id);

            // Cek duplikasi nama pada bulan dan tahun yang sama, kecuali untuk record yang sedang diupdate
            $existingRecord = LaporanCuti::where('nama', $validated['nama'])
                ->where('bulan_tahun', $validated['bulan_tahun'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama sudah terdaftar pada bulan dan tahun yang sama.',
                ]);
            }

            $laporanCuti->bulan_tahun = $validated['bulan_tahun'];
            $laporanCuti->total_cuti = $validated['total_cuti'];
            $laporanCuti->nama = $validated['nama'] ?? null;
            $laporanCuti->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data. Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $laporanCuti = LaporanCuti::findOrFail($id);
            $laporanCuti->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data. Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function getData(Request $request)
    {
        try {
            $filter = $request->input('bulan_tahun', '');
            $query = LaporanCuti::query();

            if ($filter) {
                $query->where('bulan_tahun', $filter);
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data. Error: ' . $e->getMessage(),
            ]);
        }
    }
}
