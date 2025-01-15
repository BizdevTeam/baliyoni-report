<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\RekapPiutangServisASP;

class RekapPiutangServisAspController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_pages', 12);
        $search = $request->input('search');

        $rpiutangsasps = RekapPiutangServisASP::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);

        return view('supports.rekappiutangservisasp', compact('rpiutangsasps'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'CV. ARI DISTRIBUTION CENTER',
                        'CV. BALIYONI COMPUTER',
                        'PT. NABA TECHNOLOGY SOLUTIONS',
                        'CV. ELKA MANDIRI (50%)-SAMITRA',
                        'CV. ELKA MANDIRI (50%)-DETRAN',
                    ]),
                ],
                'nilai_pendapatan' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = RekapPiutangServisASP::where('bulan', $validatedata['bulan'])
            ->where('pelaksana', $validatedata['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Simpan data ke database
            RekapPiutangServisASP::create($validatedata);

            return redirect()->route('rpiutangsasp.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Rekap Piutang data: ' . $e->getMessage());
            return redirect()->route('rpiutangsasp.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPiutangServisASP $rpiutangsasp)
    {
        try {
            // Validasi input
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'pelaksana' => [
                    'required',
                    Rule::in([
                        'CV. ARI DISTRIBUTION CENTER',
                        'CV. BALIYONI COMPUTER',
                        'PT. NABA TECHNOLOGY SOLUTIONS',
                        'CV. ELKA MANDIRI (50%)-SAMITRA',
                        'CV. ELKA MANDIRI (50%)-DETRAN',
                    ]),
                ],
                'nilai_pendapatan' => 'required|integer|min:0'
            ]);

            // Cek kombinasi unik bulan dan perusahaan
            $exists = RekapPiutangServisASP::where('bulan', $validatedata['bulan'])
            ->where('pelaksana', $validatedata['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }

            // Update data rekappiutang
            $rpiutangsasp->update($validatedata);

            return redirect()->route('rpiutangsasp.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating Rekap Piutang data: ' . $e->getMessage());
            return redirect()->route('rpiutangsasp.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(RekapPiutangServisASP $rpiutangsasp)
    {
        try {
            // Hapus data rekappiutang
            $rpiutangsasp->delete();

            return redirect()->route('rpiutangsasp.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting Rekap Piutang data: ' . $e->getMessage());
            return redirect()->route('rpiutangsasp.index')->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }
}
