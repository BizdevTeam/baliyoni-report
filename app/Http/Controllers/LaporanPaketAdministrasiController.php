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
        return view('marketings.laporanpaketadministrasi');
    }

    /**
     * Menyimpan data laporan paket administrasi.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'bulan_tahun' => 'required|date_format:m/Y', // Format bulan/tahun
            'keterangan' => 'nullable|string|max:255',
            'website' => 'required|string|max:255',
            'paket_rp' => 'required|integer|min:0',     // Nilai paket harus >= 0
        ]);

        try {
            // Simpan data ke database
            LaporanPaketAdministrasi::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Data successfully saved!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage()); // Logging error
            Log::info('Input data:', $request->all());
            Log::info($request->all()); 
            return response()->json([
                'success' => false,
                'message' => 'Error message here',
            ]); // 500 = Internal Server Error
        }
    }

    /**
     * Mengambil semua data laporan paket administrasi.
     */
    public function data()
    {
        try {
            $pakets = LaporanPaketAdministrasi::orderBy('created_at', 'desc')->get();

            // Jika data kosong, kembalikan array kosong
            if ($pakets->isEmpty()) {
                return response()->json([]);
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
