<?php

namespace App\Http\Controllers;

use App\Models\LaporanPaketAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LaporanPaketAdministrasiController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('marketings.laporanpaketadministrasi');
    }

    // Fetch data dengan filter
    public function data(Request $request)
    {
        try {
            $bulanTahun = $request->query('bulan_tahun');

            $query = LaporanPaketAdministrasi::query();
            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching data: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data.'], 500);
        }
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'website' => 'required|array|min:1',
            'website.*' => 'required|string|max:255',
            'paket_rp' => 'required|array|min:1',
            'paket_rp.*' => 'required|numeric|min:0',
        ]);

        try {
            $dataToInsert = $this->prepareDataForInsert($validated);

            LaporanPaketAdministrasi::insert($dataToInsert);

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
            'website' => 'required|array|min:1',
            'website.*' => 'required|string|max:255',
            'paket_rp' => 'required|array|min:1',
            'paket_rp.*' => 'required|numeric|min:0',
        ]);

        try {
            // Hapus data lama untuk website terkait
            LaporanPaketAdministrasi::where('id', $id)->delete();

            $dataToInsert = $this->prepareDataForInsert($validated);
            LaporanPaketAdministrasi::insert($dataToInsert);

            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error updating data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui data.'], 500);
        }
    }

    // Fungsi umum untuk save/update data
    private function saveData(Request $request, $id = null)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y',
            'website' => 'required|array|min:1',
            'website.*' => 'required|string|max:255',
            'paket_rp' => 'required|array|min:1',
            'paket_rp.*' => 'required|numeric|min:0',
        ]);

        try {
            foreach ($validated['website'] as $index => $website) {
                $query = LaporanPaketAdministrasi::where('website', $website)
                    ->where('bulan_tahun', $validated['bulan_tahun']);

                if ($id) {
                    $query->where('id', '!=', $id); // Abaikan ID yang sedang diupdate
                }

                if ($query->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Website '$website' sudah ada untuk bulan {$validated['bulan_tahun']}.",
                    ], 400);
                }
            }

            // Jika ID ada, hapus data lama (update)
            if ($id) {
                LaporanPaketAdministrasi::where('id', $id)->delete();
            }

            $dataToInsert = $this->prepareDataForInsert($validated);
            LaporanPaketAdministrasi::insert($dataToInsert);

            return response()->json([
                'success' => true,
                'message' => $id ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.',
            ], $id ? 200 : 201);
        } catch (\Exception $e) {
            Log::error("Error saving data: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    // Hapus data
    public function destroy($id)
    {
        try {
            $paket = LaporanPaketAdministrasi::findOrFail($id);
            $paket->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Data not found: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting data: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.'], 500);
        }
    }

    // Ambil data untuk Chart
    public function getChartData()
    {
        try {
            $data = LaporanPaketAdministrasi::select(
                    'website',
                    'bulan_tahun',
                    DB::raw('SUM(paket_rp) as total_rp')
                )
                ->groupBy('website', 'bulan_tahun')
                ->orderBy('bulan_tahun', 'asc')
                ->get();

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching chart data: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart.',
            ], 500);
        }
    }

    // Persiapkan data untuk di-insert ke database
    private function prepareDataForInsert($validated)
    {
        $dataToInsert = [];
        foreach ($validated['website'] as $index => $website) {
            $dataToInsert[] = [
                'bulan_tahun' => $validated['bulan_tahun'],
                'website' => $website,
                'paket_rp' => $validated['paket_rp'][$index],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $dataToInsert;
    }
}
