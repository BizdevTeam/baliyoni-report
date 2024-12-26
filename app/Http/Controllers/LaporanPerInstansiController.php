<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LaporanPerInstansiController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('marketings.laporanperinstansi');
    }

    // Fetch data dengan filter
    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');
            $query = LaporanPerInstansi::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);
        }
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'instansi' => 'required|array|min:1',
            'instansi.*' => 'required|string|max:255',
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|numeric|min:0',
        ]);

        try {   
            $dataToInsert = $this->prepareDataForInsert($validated);

            LaporanPerInstansi::insert($dataToInsert);

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    // Perbarui data
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'instansi' => 'required|array|min:1',
            'instansi.*' => 'required|string|max:255',
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|numeric|min:0',
        ]);

        try {
            // Hapus data lama untuk instansi terkait
            LaporanPerInstansi::where('id', $id)->delete();

            $dataToInsert = $this->prepareDataForInsert($validated);
            LaporanPerInstansi::insert($dataToInsert);

            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error updating data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui data.'], 500);
        }
    }

    // Hapus data
    public function destroy($id)
    {
        try {
            $paket = LaporanPerInstansi::findOrFail($id);
            $paket->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Data not found: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }

    // Persiapkan data untuk di-insert ke database
    private function prepareDataForInsert($validated)
    {
        $dataToInsert = [];
        foreach ($validated['instansi'] as $index => $instansi) {
            $dataToInsert[] = [
                'bulan_tahun' => $validated['bulan_tahun'],
                'instansi' => $instansi,
                'nilai' => $validated['nilai'][$index],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $dataToInsert;
    }
}
