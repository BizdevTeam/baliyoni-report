<?php

namespace App\Http\Controllers;
use App\Models\ArusKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArusKasController extends Controller
{
    public function index()
    {
        return view('accountings.aruskas');
    }

    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');
            $query = ArusKas::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $pakets = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $pakets,
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
            $existingEntry = ArusKas::where('bulan_tahun', $validatedData['bulan_tahun'])->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => "Data dengan bulan dan tahun yang sama sudah ada.",
                ], 400);
            }

            ArusKas::create($validatedData);

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
            $paket = ArusKas::findOrFail($id);

            // Cek duplikasi data
            $existingEntry = ArusKas::where('bulan_tahun', $validatedData['bulan_tahun'])
                ->where('id', '!=', $id) 
                ->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan bulan dan tahun yang sama sudah ada.',
                ], 400);
            }

            // Perbarui data
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

    public function destroy($id)
    {
        try {
            $paket = ArusKas::findOrFail($id);
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

    private function validateData(Request $request)
    {
        return $request->validate([
            'bulan_tahun' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],  // Ensure month/year format
            'kas_masuk' => 'required|integer|min:0',
            'kas_keluar' => 'required|integer|min:0',
        ]);
    }
}
