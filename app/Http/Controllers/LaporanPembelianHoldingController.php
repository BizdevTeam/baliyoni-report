<?php

namespace App\Http\Controllers;
use App\Models\LaporanPembelianHolding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanPembelianHoldingController extends Controller
{
    public function index()
    {
        return view('procurements.laporanpembelianholding');
    }

    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');
            $query = LaporanPembelianHolding::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $pakets = $query->orderBy('created_at', 'desc')->get();

            // Perbaiki logika totalPaket
            $totalPaket = $pakets->sum('nilai'); // Pakai tanda kutip tunggal (')

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

    public function store(Request $request)
    {
        $validatedData = $this->validateData($request);

        try {
            // Check if data already exists for the same bulan_tahun and perusahaan
            $existingEntry = LaporanPembelianHolding::where('bulan_tahun', $validatedData['bulan_tahun'])
                ->where('perusahaan', $validatedData['perusahaan'])
                ->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => "perusahaan {$validatedData['perusahaan']} sudah dipilih untuk bulan {$validatedData['bulan_tahun']}.",
                ], 400);
            }

            LaporanPembelianHolding::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage(), ['data' => $validatedData]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validateData($request);

        try {
            $paket = LaporanPembelianHolding::findOrFail($id);

            // Cek duplikasi data
            $existingEntry = LaporanPembelianHolding::where('bulan_tahun', $validatedData['bulan_tahun'])
                ->where('perusahaan', $validatedData['perusahaan'])
                ->where('id', '!=', $id) // Abaikan data dengan ID yang sama
                ->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => "perusahaan {$validatedData['perusahaan']} sudah dipilih untuk bulan {$validatedData['bulan_tahun']}.",
                ], 400);
            }

            // Perbarui data
            $paket->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating data: ' . $e->getMessage(), ['id' => $id, 'data' => $validatedData]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $paket = LaporanPembelianHolding::findOrFail($id);
            $paket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ], 500);
        }
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'bulan_tahun' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],  // Ensure month/year format
            'perusahaan' => 'required|string|max:255',
            'nilai' => 'required|integer|min:0',
        ]);
    }
}
