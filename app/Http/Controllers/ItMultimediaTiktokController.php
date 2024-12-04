<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItMultimediaTiktok;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ItMultimediaTiktokController extends Controller
{
    public function index()
    {
        $itmultimediatiktoks = ItMultimediaTiktok::all();
        return view('it.mutimediatiktok', compact('itmultimediatiktoks'));
    }

    public function store(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedata['gambar'] = $filename;
            }

            ItMultimediaTiktok::create($validatedata);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, ItMultimediaTiktok $tiktok)
    {
        try {
            $validatedata = $request->validate([
                'bulan' => 'required|date_format:Y-m',
                'keterangan' => 'required|string|max:255',
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2550'
            ]);

            if ($request->hasFile('gambar')) {
                $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
                if (File::exists($destination)) {
                    File::delete($destination);
                }

                $filename = time() . $request->file('gambar')->getClientOriginalName();
                $request->file('gambar')->move(public_path('images/it/multimediatiktok'), $filename);
                $validatedata['gambar'] = $filename;
            }

            $tiktok->update($validatedata);

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function destroy(ItMultimediaTiktok $tiktok)
    {
        try {
            $destination = "images/it/multimediatiktok/" . $tiktok->gambar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $tiktok->delete();

            return redirect()->route('tiktok.index')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting tiktok data: ' . $e->getMessage());
            return redirect()->route('tiktok.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
}
