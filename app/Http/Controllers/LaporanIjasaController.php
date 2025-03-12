<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanIjasa;
use App\Traits\DateValidationTrait;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class LaporanIjasaController extends Controller
{
    use DateValidationTrait;

        public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $search = $request->input('search');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        $query = LaporanIjasa::query();
    
        // Filter berdasarkan tanggal jika ada
        if (!empty($search)) {
            $query->where('tanggal', 'LIKE', "%$search%");
        }
    
        // Filter berdasarkan range bulan-tahun jika keduanya diisi
        if (!empty($startMonth) && !empty($endMonth)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } catch (Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid. Gunakan format Y-m.'], 400);
            }
        }
        // Ambil data dengan pagination
        $laporanijasas = $query->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) ASC')
                                  ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json(['laporanijasas' => $laporanijasas]);
        }
    
    return view('hrga.laporanijasa', compact('laporanijasas'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'jam' => 'required|date_format:H:i',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i',
            ]);


            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }

            LaporanIjasa::create($validatedData);

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }

    public function update(Request $request, LaporanIjasa $laporanijasa)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'jam' => 'nullable|date_format:H:i',
                'permasalahan' => 'required|string',
                'impact' => 'required|string',
                'troubleshooting' => 'required|string',
                'resolve_tanggal' => 'required|date',
                'resolve_jam' => 'required|date_format:H:i',
            ]);

            $errorMessage = '';
            if (!$this->isInputAllowed($validatedData['date'], $errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            }
            
            $laporanijasa->update($validatedData);

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Diupdate');
        } catch (\Exception $e) {
            Log::error('Error Updating Ijasa data: ' . $e->getMessage());
            return redirect()->route('laporanijasa.index')->with('error', 'Terjadi Kesalahan:' . $e->getMessage());
        }
    }
    public function exportPDF(Request $request)
{
    try {
        $data = $request->validate([
            'table' => 'required|string',
        ]);

        $tableHTML = trim($data['table']);

        if (empty($tableHTML)) {
            return response()->json(['success' => false, 'message' => 'Data tabel kosong.'], 400);
        }

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L', // Landscape orientation
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35, // Tambahkan margin atas untuk header teks
            'margin_bottom' => 20, // Kurangi margin bawah
            'format' => 'A4', // Ukuran kertas A4
        ]);
        $headerImagePath = public_path('images/HEADER.png'); // Sesuaikan path header
        $mpdf->SetHTMLHeader("
            <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
                <img src='{$headerImagePath}' alt='Header' style='width: 100%; height: auto;' />
            </div>
        ", 'O'); // 'O' berarti untuk halaman pertama dan seterusnya

        // Tambahkan footer ke PDF
        $mpdf->SetFooter('{DATE j-m-Y}|Laporan HRGA - Laporan iJASA|');

        $htmlContent = "
            <div style='width: 100%;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Tanggal</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Jam</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Permasalahan</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Impact</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Troubleshooting</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Tanggal</th>
                            <th style='border: 1px solid #000; padding: 1px;'>Resolve Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$tableHTML}
                    </tbody>
                </table>
            </div>
        ";

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_ijasa.pdf"');
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF.'], 500);
    }
}

    public function destroy(LaporanIjasa $laporanijasa)
    {

            $laporanijasa->delete();

            return redirect()->route('laporanijasa.index')->with('success', 'Data Berhasil Dihapus');
 
    }
}
