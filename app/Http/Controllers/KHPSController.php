<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasHutangPiutang;
use Illuminate\Support\Facades\Log;

class KHPSController extends Controller
{
    public function index(Request $request)
    { 
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');

        #$query = KasHutangPiutang::query();

        // Query untuk mencari berdasarkan tahun dan bulan
        $kashutangpiutangstoks = KasHutangPiutang::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC') // Urutkan berdasarkan tahun (descending) dan bulan (ascending)
            ->paginate($perPage);

        // Hitung total untuk masing-masing kategori
        $totalKas = $kashutangpiutangstoks->sum('kas');
        $totalHutang = $kashutangpiutangstoks->sum('hutang');
        $totalPiutang = $kashutangpiutangstoks->sum('piutang');
        $totalStok = $kashutangpiutangstoks->sum('stok');

        // Siapkan data untuk chart
        $chartData = [
            'labels' => ['Kas', 'Hutang', 'Piutang', 'Stok'],
            'datasets' => [
                [
                    'data' => [$totalKas, $totalHutang, $totalPiutang, $totalStok],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'], // Warna untuk pie chart
                    'hoverBackgroundColor' => ['#FF4757', '#3B8BEB', '#FFD700', '#00a623'],
                ],
            ],
        ];
        return view('accounting.khps', compact('kashutangpiutangstoks', 'chartData'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);
    
            KasHutangPiutang::create($validatedata);
    
            return redirect()->route('khps.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, KasHutangPiutang $khp)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'kas' => 'required|integer|min:0',
                'hutang' => 'required|integer|min:0',
                'piutang' => 'required|integer|min:0',
                'stok' => 'required|integer|min:0'
            ]);
    
            $khp->update($validatedata);
    
            return redirect()->route('khps.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(KasHutangPiutang $khp)
    {
        try {
            $khp->delete();

            return redirect()->route('khps.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting KHPS data: ' . $e->getMessage());
            return redirect()->route('khps.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
