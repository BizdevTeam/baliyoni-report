<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapPenjualan;
use Illuminate\Support\Facades\Log;

class RekapPenjualanController extends Controller
{
    // Show the view
    public function index()
    {
        return view('marketings.rekappenjualan');
    }

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

    // Filter data berdasarkan tahun
    $data = RekapPenjualan::whereYear('bulan_tahun', $tahun)->get();

    return response()->json([
        'success' => true,
        'data' => $data,
    ]);
}
    // Fetch all or filtered data
    public function filterData(Request $request)
    {
        try {
            $tahun = $request->input('tahun');
            $data = RekapPenjualan::whereRaw("RIGHT(bulan_tahun, 4) = ?", [$tahun])->get();

            // Validasi input tahun
            if (!$tahun || !is_numeric($tahun) || strlen($tahun) !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun tidak valid. Masukkan format tahun yang benar (YYYY).',
                ], 400);
            }

            $data = RekapPenjualan::whereRaw("RIGHT(bulan_tahun, 4) = ?", [$tahun])->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error filtering data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memfilter data.',
            ], 500);
        }
    }
    
    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');
            $query = RekapPenjualan::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $pakets = $query->orderBy('created_at', 'desc')->get();
            $totalPaket = $pakets->sum('total_penjualan'); // Pakai tanda kutip tunggal (')

            return response()->json([
                'success' => true,
                'data' => $pakets,
                'total_paket' => $totalPaket,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    // Store new data
    public function store(Request $request)
    {
        $validatedData = $this->validateData($request);

        try {
            // Check for duplicate data
            $existingData = RekapPenjualan::where('bulan_tahun', $validatedData['bulan_tahun'])->first();

            if ($existingData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan bulan dan tahun yang sama sudah ada.',
                ], 400);
            }

            RekapPenjualan::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    // Update existing data
    public function update(Request $request, $id)
    {
        $validatedData = $this->validateData($request);

        try {
            $paket = RekapPenjualan::findOrFail($id);

            // Prevent duplicate month-year for other records
            $existingData = RekapPenjualan::where('bulan_tahun', $validatedData['bulan_tahun'])
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
            Log::error('Error updating data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
            ], 500);
        }
    }

    // Delete data
    public function destroy($id)
    {
        try {
            $paket = RekapPenjualan::findOrFail($id);
            $paket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Data not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    // Validate input data
    private function validateData(Request $request)
    {
        return $request->validate([
            'bulan_tahun' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],
            'total_penjualan' => 'required|integer|min:0',
        ]);
    }
}
