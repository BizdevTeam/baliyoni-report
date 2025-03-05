<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil input tanggal mulai dan tanggal akhir dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $evaluasis = Evaluasi::query()
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan waktu pembuatan terbaru
            ->paginate(10)
            ->withQueryString();

        return view('evaluasi.evaluasi', compact('evaluasis'));
        
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
            'target_realisasi' => 'required|string',
            'analisa_penyimpangan' => 'required|string',
            'alternative_solusi' => 'required|string',
        ]);

        // Adding the current date and time to the request data
        $data = $request->all();
        $data['created_at'] = now();  // Use Laravel's now() helper for the current date and time

        // Creating a new question with the additional created_at field
        Evaluasi::create($data);

        return redirect()->route('evaluasi.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function update(Request $request, Evaluasi $evaluasi)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
            'target_realisasi' => 'required|string',
            'analisa_penyimpangan' => 'required|string',
            'alternative_solusi' => 'required|string',
        ]);

        $evaluasi->update($request->all());

        return redirect()->route('evaluasi.index')->with('success', 'Data Berhasil Diubah');
    }

    public function destroy(Evaluasi $evaluasi)
    {
        $evaluasi->delete();

        return redirect()->route('evaluasi.index')->with('success', 'Data Berhasil Dihapus');
    }
}
