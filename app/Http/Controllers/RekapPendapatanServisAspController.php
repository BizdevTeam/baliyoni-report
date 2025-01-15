<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\RekapPendapatanServisAsp;

class RekapPendapatanServisAspController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_pages', 12);
        $search = $request->input('search');

        $rpsasps = RekapPendapatanServisAsp::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'Like', "%$search%")
                             ->orWhere('pelaksana', 'like', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);

        return view('supports.rekappendapatanservisasp', compact('rpsasps'));
    }

    public function store(Request $request)
    {
        try {
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
            $exists = RekapPendapatanServisAsp::where('bulan', $validatedata['bulan'])
            ->where('pelaksana', $validatedata['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            RekapPendapatanServisAsp::create($validatedata);
    
            return redirect()->route('rpsasp.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing RPSASP data: ' . $e->getMessage());
            return redirect()->route('rpsasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, RekapPendapatanServisAsp $rpsasp)
    {
        try {
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
            $exists = RekapPendapatanServisAsp::where('bulan', $validatedata['bulan'])
            ->where('pelaksana', $validatedata['pelaksana'])
            ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Data Already Exists.');
            }
    
            $rpsasp->update($validatedata);
    
            return redirect()->route('rpsasp.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating RPSASP data: ' . $e->getMessage());
            return redirect()->route('rpsasp.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(RekapPendapatanServisAsp $rpsasp)
    {
        $rpsasp->delete();

        return redirect()->route('rpsasp.index')->with('success', 'Data Berhasil Dihapus');
    }
}
