<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use Illuminate\Support\Facades\Log;

class LaporanIjasaController extends Controller
{
    public function index()
    {
        return view('hrga.laporanijasa');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date_format:Y-m',
            'jam' => 'required|date_format:H:i',
            'permasalahan' => 'required|string|max:255',
            'impact' => 'required|string|max:255',
            'troubleshooting' => 'required|string|max:255',
            'resolve_tanggal' => 'required|date',
            'resolve_jam' => 'required|date_format:H:i',
        ]);

        try {
            LaporanIjasa::create($validated);
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error("Error Store Laporan: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data.']);
        }
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date_format:Y-m',
            'jam' => 'required|date_format:H:i',
            'permasalahan' => 'required|string|max:255',
            'impact' => 'required|string|max:255',
            'troubleshooting' => 'required|string|max:255',
            'resolve_tanggal' => 'required|date',
            'resolve_jam' => 'required|date_format:H:i',
        ]);

        try {
            $laporanIjasa = LaporanIjasa::findOrFail($id);
            $laporanIjasa->update($validated);
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error("Error Update Laporan: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.']);
        }
    }

    public function destroy($id)
    {
        try {
            $laporanIjasa = LaporanIjasa::findOrFail($id);
            $laporanIjasa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error Delete Laporan: " . $e->getMessage());
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
            $query = LaporanIjasa::query();

            if ($filter) {
                $query->where('tanggal', 'LIKE', $filter . '%');
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error("Error Fetch Laporan Data: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data. Error: ' . $e->getMessage(),
            ]);
        }
    }
}
