<?php

namespace App\Http\Controllers;

use App\Models\ItBizdevBulanan;
use App\Models\ItBizdevData;
use Illuminate\Http\Request;

class ItBizdevDataController extends Controller
{
    /**
     * Tampilkan data berdasarkan bulan yang dipilih.
     */
    public function index($bizdevbulanan_id)
    {
        // Cari bulan berdasarkan ID
        $bizdevbulanan = ItBizdevBulanan::findOrFail($bizdevbulanan_id);

        // Ambil semua data terkait bulan tersebut
        $itbizdevdatas = $bizdevbulanan->datas;

        return view('it.bizdevdata', compact('bizdevbulanan', 'itbizdevdatas'));
    }

    /**
     * Tampilkan form untuk menambahkan data baru.
     */
    public function create($bizdevbulanan_id)
    {
        $itbizdevbulanan = ItBizdevBulanan::findOrFail($bizdevbulanan_id);
        return view('it.bizdevdata', compact('itbizdevbulanan'));
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request, $bizdevbulanan_id)
    {
        // Gunakan $bizdevbulanan_id untuk mencari atau membuat relasi
        $bizdevbulanan = ItBizdevBulanan::findOrFail($bizdevbulanan_id);

        // Validasi data
        $validatedata = $request->validate([
            'aplikasi' => 'required|string',
            'kondisi_bulanlalu' => 'required|string',
            'kondisi_bulanini' => 'required|string',
            'update' => 'required|string',
            'rencana_implementasi' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan data
        $bizdevbulanan->datas()->create($validatedata);

        return redirect()->route('bizdevdata.index', $bizdevbulanan_id)->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Tampilkan form untuk mengedit data.
     */
    public function edit($bizdevbulanan_id, $id_bizdevdata)
    {
        $bizdevbulanan = ItBizdevBulanan::findOrFail($bizdevbulanan_id);
        $itbizdevdata = ItBizdevData::findOrFail($id_bizdevdata);

        return view('bizdevdata.edit', compact('bizdevbulanan', 'itbizdevdata'));
    }

    /**
     * Perbarui data di database.
     */
    public function update(Request $request, $bizdevbulanan_id, $id_bizdevdata)
    {
        // Validasi data
        $validated = $request->validate([
            'aplikasi' => 'required|string',
            'kondisi_bulanlalu' => 'required|string',
            'kondisi_bulanini' => 'required|string',
            'update' => 'required|string',
            'rencana_implementasi' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Cari data berdasarkan ID dan update
        $itbizdevdata = ItBizdevData::findOrFail($id_bizdevdata);
        $itbizdevdata->update($validated);

        return redirect()->route('bizdevdata.index', $bizdevbulanan_id)->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Hapus data dari database.
     */
    public function destroy($bizdevbulanan_id, $id_bizdevdata)
    {
        $itbizdevdata = ItBizdevData::findOrFail($id_bizdevdata);
        $itbizdevdata->delete();

        return redirect()->route('bizdevdata.index', $bizdevbulanan_id)->with('success', 'Data berhasil dihapus.');
    }
}
