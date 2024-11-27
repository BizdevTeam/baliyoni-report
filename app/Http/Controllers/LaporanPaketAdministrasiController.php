<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPaketAdministrasi;
use Illuminate\Support\Facades\Log; // Pastikan logging menggunakan Log Laravel

class LaporanPaketAdministrasiController extends Controller
{
    /**
     * Menampilkan halaman laporan paket administrasi.
     */
    public function index()
    {
        LaporanPaketAdministrasi::all();
        return view('marketings.laporanpaketadministrasi');
    }

    public function getData()
    {
        LaporanPaketAdministrasi::all();
    }
    /**
     * Menyimpan data laporan paket administrasi.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'bulan_tahun' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'], // Format bulan/tahun
            'keterangan' => 'nullable|string|max:255',
            'website' => 'required|string|max:255',
            'paket_rp' => 'required|integer|min:0',    // Nilai paket harus >= 0
        ]);

        try {
            // Cek apakah kombinasi bulan/tahun dan website sudah ada
            $existingEntry = LaporanPaketAdministrasi::where('bulan_tahun', $validatedData['bulan_tahun'])
                ->where('website', $validatedData['website'])
                ->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => "Website {$validatedData['website']} sudah dipilih untuk bulan {$validatedData['bulan_tahun']}.",
                ], 400); // 400 = Bad Request
            }

            // Simpan data ke database
            LaporanPaketAdministrasi::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data successfully saved!',
            ]);
        } catch (\Exception $e) {
            // Logging error dan data input
            Log::error('Error saving data: ' . $e->getMessage());
            Log::info('Input data:', $request->all());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving data.',
            ], 500); // 500 = Internal Server Error
        }
    }

    /**
     * Mengambil semua data laporan paket administrasi.
     */
    public function data(Request $request)
    {
        try {
            // Ambil parameter filter bulan/tahun jika ada
            $bulanTahun = $request->query('bulan_tahun');

            // Query data dengan atau tanpa filter bulan/tahun
            $query = LaporanPaketAdministrasi::query();

            if ($bulanTahun) {
                $query->where('bulan_tahun', $bulanTahun);
            }

            $pakets = $query->orderBy('created_at', 'desc')->get();

            // Jika data kosong, kembalikan array kosong
            if ($pakets->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ], 200); // 200 = OK
            }

            return response()->json([
                'success' => true,
                'data' => $pakets,
            ], 200); // 200 = OK
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage()); // Logging error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage(),
            ], 500); // 500 = Internal Server Error
        }
    }
}
