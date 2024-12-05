<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPtBos;
use Illuminate\Support\Facades\Log;

class LaporanPtBosController extends Controller
{
    public function index()
    {
        return view('hrga.laporanptbos');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'pekerjaan' => 'required|string|max:255',
            'kondisi_bulan_lalu' => 'required|string|max:255',
            'kondisi_bulan_ini' => 'required|string|max:255',
            'update' => 'required|string|max:255',
            'rencana_implementasi' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            $laporanptbos = new laporanptbos();
            $laporanptbos->bulan_tahun = $validated['bulan_tahun'];
            $laporanptbos->pekerjaan = $validated['pekerjaan'];
            $laporanptbos->kondisi_bulan_lalu = $validated['kondisi_bulan_lalu'];
            $laporanptbos->kondisi_bulan_ini = $validated['kondisi_bulan_ini'];
            $laporanptbos->update = $validated['update'];
            $laporanptbos->rencana_implementasi = $validated['rencana_implementasi'];
            $laporanptbos->keterangan = $validated['keterangan'];
            $laporanptbos->save();

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
            'pekerjaan' => 'required|string|max:255',
            'kondisi_bulan_lalu' => 'required|string|max:255',
            'kondisi_bulan_ini' => 'required|string|max:255',
            'update' => 'required|string|max:255',
            'rencana_implementasi' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            $laporanPtbos = laporanPtbos::findOrFail($id);
            $laporanPtbos->bulan_tahun = $validated['bulan_tahun'];
            $laporanPtbos->pekerjaan = $validated['pekerjaan'];
            $laporanPtbos->kondisi_bulan_lalu = $validated['kondisi_bulan_lalu'];
            $laporanPtbos->kondisi_bulan_ini = $validated['kondisi_bulan_ini'];
            $laporanPtbos->update = $validated['update'];
            $laporanPtbos->rencana_implementasi = $validated['rencana_implementasi'];
            $laporanPtbos->keterangan = $validated['keterangan'];
            $laporanPtbos->save();

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
            $laporanPtbos = laporanPtbos::findOrFail($id);
            $laporanPtbos->delete();

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
            $query = laporanPtbos::query();

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
