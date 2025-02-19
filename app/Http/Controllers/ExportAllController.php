<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\LaporanHolding;
use App\Models\LaporanPaketAdministrasi;
use App\Models\RekapPenjualan;
use App\Models\RekapPenjualanPerusahaan;
use App\Models\StatusPaket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use ZipArchive;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use App\Models\LaporanPerInstansi;

class ExportAllController extends Controller
{
    public function exportAll(Request $request) {
        $search = $request->input('search', '');
        
        // Create temporary directory for PDFs
        $tempDir = storage_path('app/temp/exports/' . uniqid());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }   

        // Generate all PDFs
        $files = [
            // 'rekap_penjualan.pdf' => $this->exportPenjualan($search),
            // 'rekap_perusahaan.pdf' => $this->exportPerusahaan($search),
            // 'laporan_holding.pdf' => $this->exportHolding($search),
            // 'paket_administrasi.pdf' => $this->exportPaketAdministrasi($search),
            // 'status_paket.pdf' => $this->exportStatusPaket($search),
            'per_instansi.pdf' => $this->exportPerInstansi($search),
        ];

        // If only one file is requested, return it directly
        if (count($files) === 1) {
            $content = reset($files);
            $filename = key($files);
            return response($content)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        // Create ZIP archive
        $zipName = 'laporan_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = $tempDir . '/' . $zipName;
        
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);

        // Add files to ZIP
        foreach ($files as $filename => $content) {
            $filePath = $tempDir . '/' . $filename;
            file_put_contents($filePath, $content);
            $zip->addFile($filePath, $filename);
        }
        
        $zip->close();

        // Send ZIP file
        $response = response()->download($zipPath, $zipName);

        // Cleanup handler
        $response->deleteFileAfterSend(true);

        return $response;
    }

    //export rekap penjualan
    private function exportPenjualan(string $search) {
        $rekappenjualans = RekapPenjualan::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get();
    
        // Format currency for display
        $rekappenjualans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });
    
        // Generate chart image
        $chartImagePath = $this->generateChartImage($rekappenjualans);
    
        // Render view with data
        $html = View::make('exports.rekap-penjualan', [
            'rekappenjualans' => $rekappenjualans,
            'chartImagePath' => $chartImagePath
        ])->render();
    
        // Configure mPDF
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
            'format' => 'A4-L',
        ]);
    
        $mpdf->WriteHTML($html);
        
        // Clean up temporary chart image
        if (file_exists($chartImagePath)) {
            unlink($chartImagePath);
        }
        
        return $mpdf->Output('', 'S');
    }
    
    private function generateChartImage($rekappenjualans) {
        // Create data arrays for the chart
        $data = $rekappenjualans->pluck('total_penjualan')->toArray();
        $labels = $rekappenjualans->map(function($item) {
            return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        })->toArray();
    
        // Create the graph
        $graph = new Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->SetBox(false);
        
        // Create the bar plot
        $bplot = new Plot\BarPlot($data);
        $bplot->SetFillColor('blue@0.7');
        
        // Add the plot to the graph
        $graph->Add($bplot);
        
        // Set the titles
        $graph->title->Set('Grafik Penjualan per Bulan');
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        
        // Format Y-axis labels as currency
        $graph->yaxis->SetLabelFormatCallback(function($value) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        });
    
        // Generate temporary file path
        $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
        
        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        
        // Save the graph
        $graph->Stroke($tempPath);
        
        return $tempPath;
    }
    
    //export penjualan perusahaan
    private function exportPerusahaan(string $search) {
        $rekappenjualanperusahaans = RekapPenjualanPerusahaan::with('perusahaan')
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%")
                             ->orWhereHas('perusahaan', function ($q) use ($search) {
                                 $q->where('nama_perusahaan', 'LIKE', "%$search%");
                             });
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get(); // 🔹 Tambahkan get() untuk mengubah query menjadi collection
    
        // Format currency for display
        $rekappenjualanperusahaans->map(function ($item) {
            $item->total_penjualan_formatted = 'Rp ' . number_format($item->total_penjualan, 0, ',', '.');
            return $item;
        });
    
        // Generate chart image
        $chartImagePath = $this->generateChartImage1($rekappenjualanperusahaans); // 🔹 Hapus double $
    
        // Render view with data
        $html = View::make('exports.rekap-perusahaan', [
            'rekappenjualanperusahaans' => $rekappenjualanperusahaans, // 🔹 Hapus double $
            'chartImagePath' => $chartImagePath
        ])->render();
    
        // Configure mPDF
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
            'format' => 'A4-L',
        ]);
    
        $mpdf->WriteHTML($html);
        
        // Clean up temporary chart image
        if (file_exists($chartImagePath)) {
            unlink($chartImagePath);
        }
        
        return $mpdf->Output('', 'S');
    }
    
    private function generateChartImage1($rekappenjualanperusahaans) {
        // Create data arrays for the chart
        $data = $rekappenjualanperusahaans->pluck('total_penjualan')->toArray(); // 🔹 Hapus double $
        $labels = $rekappenjualanperusahaans->map(function($item) { // 🔹 Hapus double $
            return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        })->toArray();
    
        // Create the graph
        $graph = new Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->SetBox(false);
        
        // Create the bar plot
        $bplot = new Plot\BarPlot($data);
        $bplot->SetFillColor('blue@0.7');
        
        // Add the plot to the graph
        $graph->Add($bplot);
        
        // Set the titles
        $graph->title->Set('Grafik Penjualan per Bulan');
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        
        // Format Y-axis labels as currency
        $graph->yaxis->SetLabelFormatCallback(function($value) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        });
    
        // Generate temporary file path
        $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
        
        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        
        // Save the graph
        $graph->Stroke($tempPath);
        
        return $tempPath;
    }
    
    private function exportHolding(string $search) {
        $laporanholdings = LaporanHolding::with('perusahaan')
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%")
                    ->orWhereHas('perusahaan', function ($q) use ($search) {
                        $q->where('nama_perusahaan', 'LIKE', "%$search%");
                    });
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get();
        
                // Format currency for display
                $laporanholdings->map(function ($item) {
                    $item->nilai_formatted = 'Rp ' . number_format($item->nilai, 0, ',', '.');
                    return $item;
                });

       // Generate chart image
       $chartImagePath = $this->generateChartImage2($laporanholdings);
    
       // Render view with data
       $html = View::make('exports.laporan-holding', [
           'laporanholdings' => $laporanholdings,
           'chartImagePath' => $chartImagePath
       ])->render();
   
       // Configure mPDF
       $mpdf = new Mpdf([
           'margin_left' => 10,
           'margin_right' => 10,
           'margin_top' => 15,
           'margin_bottom' => 15,
           'mode' => 'utf-8',
           'format' => 'A4-L',
       ]);
   
       $mpdf->WriteHTML($html);
       
       // Clean up temporary chart image
       if (file_exists($chartImagePath)) {
           unlink($chartImagePath);
       }
       
       return $mpdf->Output('', 'S');
   }
   
   private function generateChartImage2($laporanholdings) {
       // Create data arrays for the chart
       $data = $laporanholdings->pluck('nilai')->toArray();
       $labels = $laporanholdings->map(function($item) {
           return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
       })->toArray();
   
       // Create the graph
       $graph = new Graph\Graph(800, 400);
       $graph->SetScale('textlin');
       $graph->SetBox(false);
       
       // Create the bar plot
       $bplot = new Plot\BarPlot($data);
       $bplot->SetFillColor('blue@0.7');
       
       // Add the plot to the graph
       $graph->Add($bplot);
       
       // Set the titles
       $graph->title->Set('Grafik Penjualan per Bulan');
       $graph->xaxis->SetTickLabels($labels);
       $graph->xaxis->SetLabelAngle(45);
       
       // Format Y-axis labels as currency
       $graph->yaxis->SetLabelFormatCallback(function($value) {
           return 'Rp ' . number_format($value, 0, ',', '.');
       });
   
       // Generate temporary file path
       $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
       
       // Ensure directory exists
       if (!file_exists(dirname($tempPath))) {
           mkdir(dirname($tempPath), 0777, true);
       }
       
       // Save the graph
       $graph->Stroke($tempPath);
       
       return $tempPath;
   }

    private function exportPaketAdministrasi(string $search) {
        $laporanpaketadministrasis = LaporanPaketAdministrasi::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get();

            $laporanpaketadministrasis->map(function ($item) {
                $item->total_paket_formatted = number_format($item->total_paket, 0, ',', '.');
                return $item;
            });

        // Generate chart image
        $chartImagePath = $this->generateChartImage3($laporanpaketadministrasis);
    
        // Render view with data
        $html = View::make('exports.laporan-paketa', [
            'laporanpaketadministrasis' => $laporanpaketadministrasis,
            'chartImagePath' => $chartImagePath
        ])->render();
    
        // Configure mPDF
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
            'format' => 'A4-L',
        ]);
    
        $mpdf->WriteHTML($html);
        
        // Clean up temporary chart image
        if (file_exists($chartImagePath)) {
            unlink($chartImagePath);
        }
        
        return $mpdf->Output('', 'S');
    }
    
    private function generateChartImage3($laporanpaketadministrasis) {
        // Create data arrays for the chart
        $data = $laporanpaketadministrasis->pluck('total_paket');
        $labels = $laporanpaketadministrasis->map(function($item) {
            return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        })->toArray();
    
        // Create the graph
        $graph = new Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->SetBox(false);
        
        // Create the bar plot
        $bplot = new Plot\BarPlot($data);
        $bplot->SetFillColor('blue@0.7');
        
        // Add the plot to the graph
        $graph->Add($bplot);
        
        // Set the titles
        $graph->title->Set('Grafik Paket Administrasi per Bulan');
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        
        // Format Y-axis labels as currency
        $graph->yaxis->SetLabelFormatCallback(function($value) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        });
        // Generate temporary file path
        $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
        
        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        // Save the graph
        $graph->Stroke($tempPath);
        
        return $tempPath;
    }

    private function exportStatusPaket(string $search) {
        $statuspakets = StatusPaket::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get();

            $statuspakets->map(function ($item) {
                $item->total_paket_formatted = number_format($item->total_paket, 0, ',', '.');
                return $item;
            });

        // Generate chart image
        $chartImagePath = $this->generateChartImage4($statuspakets);
    
        // Render view with data
        $html = View::make('exports.laporan-status', [
            'statuspakets' => $statuspakets,
            'chartImagePath' => $chartImagePath
        ])->render();
    
        // Configure mPDF
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
            'format' => 'A4-L',
        ]);
    
        $mpdf->WriteHTML($html);
        
        // Clean up temporary chart image
        if (file_exists($chartImagePath)) {
            unlink($chartImagePath);
        }
        
        return $mpdf->Output('', 'S');
    }
    
    private function generateChartImage4($statuspakets) {
        // Create data arrays for the chart
        $data = $statuspakets->pluck('total_paket');
        $labels = $statuspakets->map(function($item) {
            return \Carbon\Carbon::parse($item->bulan)->translatedFormat('F - Y');
        })->toArray();
    
        // Create the graph
        $graph = new Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->SetBox(false);
        
        // Create the bar plot
        $bplot = new Plot\BarPlot($data);
        $bplot->SetFillColor('blue@0.7');
        
        // Add the plot to the graph
        $graph->Add($bplot);
        
        // Set the titles
        $graph->title->Set('Grafik Paket Administrasi per Bulan');
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        
        // Format Y-axis labels as currency
        $graph->yaxis->SetLabelFormatCallback(function($value) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        });
        // Generate temporary file path
        $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
        
        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        // Save the graph
        $graph->Stroke($tempPath);
        
        return $tempPath;
    }

    //Export Per Instansi
    private function exportPerInstansi(string $search) {
        $laporanperinstansis = LaporanPerInstansi::query()
            ->when($search, function ($query, $search) {
                return $query->where('bulan', 'LIKE', "%$search%");
            })
            ->orderByRaw('YEAR(bulan) DESC, MONTH(bulan) ASC')
            ->get();

            $laporanperinstansis->map(function ($item) {
                $item->nilai_formatted = number_format($item->nilai, 0, ',', '.');
                return $item;
            });

        // Generate chart image
        $chartImagePath = $this->generateChartImage5($laporanperinstansis);
    
        // Render view with data
        $html = View::make('exports.per-instansi', [
            'laporanperinstansis' => $laporanperinstansis,
            'chartImagePath' => $chartImagePath
        ])->render();
    
        // Configure mPDF
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
            'format' => 'A4-L',
        ]);
    
        $mpdf->WriteHTML($html);
        
        // Clean up temporary chart image
        if (file_exists($chartImagePath)) {
            unlink($chartImagePath);
        }
        
        return $mpdf->Output('', 'S');
    }
    
    private function generateChartImage5($laporanperinstansis) {
        // Create data arrays for the chart
        $data = $laporanperinstansis->pluck('nilai');
        $labels = $laporanperinstansis->pluck('instansi')->toArray();
    
        // Create the graph
        $graph = new Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->SetBox(false);
        
        // Create the bar plot
        $bplot = new Plot\BarPlot($data);
        $bplot->SetFillColor('blue@0.7');
        
        // Add the plot to the graph
        $graph->Add($bplot);
        
        // Set the titles
        $graph->title->Set('Grafik Per Instansi per Bulan');
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        
        // Format Y-axis labels as currency
        $graph->yaxis->SetLabelFormatCallback(function($value) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        });
        // Generate temporary file path
        $tempPath = storage_path('app/temp/charts/' . uniqid() . '.png');
        
        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        // Save the graph
        $graph->Stroke($tempPath);
        
        return $tempPath;
    }


    private function template($data1, $data2)  {
        return  $htmlContent = "
        <div style='gap: 100px; width: 100%;'>
            <div style='width: 30%; float: left; padding-right: 20px;'>
                <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
                <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                    <thead>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='border: 1px solid #000; padding: 1px;'>Bulan</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Instansi</th>
                            <th style='border: 1px solid #000; padding: 2px;'>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$data1}
                    </tbody>
                </table>
            </div>
            <div style='width: 65%; text-align:center; margin-left: 20px;'>
                <h2 style='font-size: 14px; margin-bottom: 10px;'>Grafik Laporan Per Instansi</h2>
                <img src='{$data2}' style='width: 100%; height: auto;' alt='Grafik Laporan' />
            </div>
        </div>
        ";
    }
}