<?php

namespace App\Http\Controllers;

use App\Models\LaporanPtBos;
use Illuminate\Http\Request;

class LaporanPtBosController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);
        $search = $request->input('search');

        $laporanptboss = LaporanPtBos::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->paginate($perPage);
        
        return view('hrga.laporanptbos', compact('laporanptboss'));
    }
}
