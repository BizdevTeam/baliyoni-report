<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanStok;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LaporanStokController extends Controller
{
    /**
     * Tampilkan halaman laporan stok.
     */
    public function index()
    {
        return view('procurements.laporanstok');
    }

    /**
     * Filter data berdasarkan tahun.
     */
    public function filterByYear(Request $request)
    {
        $tahun = $request->query('tahun');

        // Validasi tahun
        if (!preg_match('/^\d{4}$/', $tahun)) {
            return response()->json([
                'success' => false,
                'message' => 'Format tahun tidak valid.',
            ], 400);
        }

        try {
            $data = LaporanStok::whereYear('bulan_tahun', $tahun)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering data by year: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memfilter data.',
            ], 500);
        }
    }

    /**
     * Filter data berdasarkan parameter lainnya.
     */
    public function filterData(Request $request)
    {
        $tahun = $request->input('tahun');

        // Validasi input tahun
        if (!$tahun || !preg_match('/^\d{4}$/', $tahun)) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak valid. Masukkan format tahun yang benar (YYYY).',
            ], 400);
        }

        try {
            $data = LaporanStok::whereYear('bulan_tahun', $tahun)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memfilter data.',
            ], 500);
        }
    }

    /**
     * Ambil data untuk laporan stok.
     */
    public function data(Request $request)
    {
        $bulanTahun = $request->query('bulan_tahun');

        try {
            $query = LaporanStok::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $pakets = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $pakets,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateData($request);

        try {
            // Periksa duplikasi
            $existingData = LaporanStok::where('bulan_tahun', $validatedData['bulan_tahun'])->first();

            if ($existingData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan bulan dan tahun yang sama sudah ada.',
                ], 400);
            }

            LaporanStok::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error saving data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    /**
     * Perbarui data yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $this->validateData($request);

        try {
            $paket = LaporanStok::findOrFail($id);

            // Periksa duplikasi untuk data lain
            $existingData = LaporanStok::where('bulan_tahun', $validatedData['bulan_tahun'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan bulan dan tahun yang sama sudah ada.',
                ], 400);
            }

            $paket->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
            ], 500);
        }
    }

    /**
     * Ambil data untuk chart.
     */
    public function getChartData(Request $request)
    {
        try {
            // Filter berdasarkan tahun jika ada parameter tahun
            $tahun = $request->query('tahun');
            $query = LaporanStok::query();

            if ($tahun && preg_match('/^\d{4}$/', $tahun)) {
                $query->whereYear('bulan_tahun', $tahun);
            }

            // Grouping dan perhitungan data untuk chart
            $data = $query->select(
                DB::raw("DATE_FORMAT(bulan_tahun, '%m/%Y') as bulan_tahun"),
                DB::raw('SUM(stok) as total_stok')
            )
                ->groupBy('bulan_tahun')
                ->orderByRaw('YEAR(bulan_tahun), MONTH(bulan_tahun)')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching chart data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart.',
            ], 500);
        }
    }

    /**
     * Hapus data berdasarkan ID.
     */
    public function destroy($id)
    {
        try {
            $paket = LaporanStok::findOrFail($id);
            $paket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Data not found: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    /**
     * Validasi input data.
     */
    private function validateData(Request $request)
    {
        return $request->validate([
            'bulan_tahun' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'], // Format MM/YYYY
            'stok' => 'required|integer|min:0',
        ]);
    }
}
