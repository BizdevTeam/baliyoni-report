<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Export Rekap Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* th, td { border: 1px solid #000; padding: 4px; text-align: left; } */
        th {
            background-color: #f2f2f2;
        }
    </style>

    <style>
        @media print {
        .page-break {
            page-break-before: always;
        }
    
        .page {
            page-break-inside: avoid;
        }
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .table-container {
        max-height: 600px;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .table-wrapper {
            max-height: 600px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        table, th, td {
    border: 1px solid black;
    }
    th, td {
        padding: 4px;
        font-size: 12px;
        text-align: center;
        font-family: serif;
    }
    thead {
        background-color: #f0f0f0;
    }

}
    </style>
    {{-- <style>
        @media print {
            @page {
            margin: 0;
            size: A4 landscape;
            }
            .page {
                page-break-after: always;
                page-break-inside: avoid;
                position: relative;
                min-height: 100%;
            }
            .header, .footer {
                position: relative;
                width: 100%;
            }
            .content {
                padding-top: 100px;
                padding-bottom: 50px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
        .page {
            width: 100%;
            min-height: 100%;
            padding: 20px;
            background: white;
        }
        .table-wrapper {
            max-height: 600px;
        }
        </style>         --}}

<style>
    /* Styling agar numbered list & bullet list tetap tampil di tabel */
    .content-html ol {
    list-style-type: decimal;
    margin-left: 20px;
    }

    .content-html ul {
    list-style-type: disc;
    margin-left: 20px;
    }

    .content-html li {
    margin-bottom: 4px;
    }
    </style>
    
    </head>
<body onload="window.print()">

    <div id="exportContent">

        <!-- === DIVISI MARKETING === -->

        <div class="page">
        <!-- Export Page 1 -->
    
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanPenjualan['rekap']) || count($dataExportLaporanPenjualan['rekap']) === 0)
                        <tr>
                            <td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanPenjualan['rekap'] as $LaporanPenjualan)
                        <tr>
                            <td class="border border-black p-1 text-start text-[10px] font-serif">{{ $LaporanPenjualan['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-start text-[10px] font-serif">{{ $LaporanPenjualan['Total Penjualan'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Rekap Penjualan</p>
        </div>
        </div>

        <!-- === Page Break 2 === -->
        <div class="page-break"></div>

        <!-- Export Page 2 -->
        <div id="pagesContainer">

            <div class="page">
                <!-- Header -->
                <div class="header">
                    <img src="{{ asset('images/HEADER.png') }}" alt="Header">
                </div>
        
                <!-- Content -->
            <div class="content">
            <div class="flex justify-between p-6">
                <div class="width-1/2 pr-10">
                    <h2 class="text-center font-serif">Tabel Data</h2>
                    <div class="table-wrapper">
                        <table id="rekapTable" class="dataTable border border-black">
                            <thead>
                                <tr>
                                    <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                    <th class="border border-black p-1 text-center text-[10px] font-serif">Perusahaan</th>
                                    <th class="border border-black p-1 text-center text-[10px] font-serif">Total Penjualan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody id="rekapTableBody">
                                @foreach($dataExportLaporanPenjualanPerusahaan['rekap'] as $LaporanPenjualanPerusahaan)
                                <tr>
                                    <td class="border border-black p-1 text-center text-[10px]">{{ $LaporanPenjualanPerusahaan['Tanggal'] }}</td>
                                    <td class="border border-black p-1 text-center text-[10px]">{{ $LaporanPenjualanPerusahaan['Perusahaan'] }}</td>
                                    <td class="border border-black p-1 text-center text-[10px]">{{ $LaporanPenjualanPerusahaan['Total Penjualan'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                    <div id="chartContainer">
                        <h2 class="text-center font-serif">Grafik Laporan</h2>
                        <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualanPerusahaan["chart"])'></canvas>
                    </div>
                </div>
            </div>
        
                <!-- Footer -->
                <div class="footer border-t text-center pt-4 z-50 bg-white">
                    <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Rekap Penjualan Perusahaan</p>
                </div>
            </div>
        </div>
        

        <!-- === Page Break 3 === -->
        <div class="page-break"></div>
        
        <!-- Export Page 3 -->
        <!-- === Page 3 === -->
        <div class="page">

            <!-- buat header disini  -->
            <div>
                <img src={{ "images/HEADER.png" }} alt="">
            </div>
    
            <div class="flex justify-between p-6">
                <!-- Tabel Data untuk ekspor PDF -->
                <div class="width-1/2 pr-10">
                    <h2 class="text-center font-serif">Tabel Data</h2>
                    <table id="rekapTable" class="dataTable">
                        <thead>
                            <tr>
                                <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                <th class="border border-black p-1 text-center text-[10px] font-serif">Website</th>
                                <th class="border border-black p-1 text-center text-[10px] font-serif">Total Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($dataExportLaporanPaketAdministrasi['rekap']) || count($dataExportLaporanPaketAdministrasi['rekap']) === 0)
                            <tr>
                                <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                            </tr>
                            @else
                            @foreach($dataExportLaporanPaketAdministrasi['rekap'] as $LaporanPaketAdministrasi)
                            <tr>
                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPaketAdministrasi['Tanggal'] }}</td>
                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPaketAdministrasi['Website'] }}</td>
                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPaketAdministrasi['Total Paket'] }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Grafik untuk ekspor PDF -->
                <div id="chartContainer">
                    <h2 class="text-center font-serif">Grafik Laporan</h2>
                    <canvas class="chart-export-paket w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPaketAdministrasi["chart"])'></canvas>
                </div>
            </div>  
    
        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Paket Administrasi</p>
        </div>
        </div>


        <!-- === Page Break 4 === -->
        <div class="page-break"></div>
        <!-- Export Page 4 -->
        <!-- === Page 4 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Status</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Paket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportStatusPaket['rekap']) || count($dataExportStatusPaket['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportStatusPaket['rekap'] as $StatusPaket)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $StatusPaket['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $StatusPaket['Status'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $StatusPaket['Total Paket'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-paket w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportStatusPaket["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Status Paket</p>
        </div>
        </div>

        <!-- === DIVISI PROCUREMENTS === -->
        <!-- === Page Break 6 === -->
        <div class="page-break"></div>
        <!-- Export Page 6 -->
        <!-- === Page 6 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Instansi</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanPerInstansi['rekap']) || count($dataExportLaporanPerInstansi['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanPerInstansi['rekap'] as $LaporanPerInstansi)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPerInstansi['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPerInstansi['Instansi'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanPerInstansi['Nilai'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPerInstansi["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Per Instansi</p>
        </div>
        </div>

        <!-- === Page Break 6 === -->
        <div class="page-break"></div>
        <!-- Export Page 6 -->
        <!-- === Page 6 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Instansi</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanHolding['rekap']) || count($dataExportLaporanHolding['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanHolding['rekap'] as $LaporanHolding)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanHolding['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanHolding['Perusahaan'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanHolding['Nilai'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanHolding["chart"])'></canvas>
            </div>
        </div>  

            <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Holding</p>
            </div>
        </div>

        <!-- === Page Break 7 === -->
        <div class="page-break"></div>
        <!-- Export Page 7 -->
        <!-- === Page 7 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanStok['rekap']) || count($dataExportLaporanStok['rekap']) === 0)
                        <tr>
                            <td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanStok['rekap'] as $LaporanStok)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanStok['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanStok['Stok'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanStok["chart"])'></canvas>
            </div>
        </div>  

            <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Stok</p>
            </div>
        </div>

        <!-- === Page Break 8 === -->
        <div class="page-break"></div>
        <!-- Export Page 8 -->
        <!-- === Page 8 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanPembelianOutlet['rekap']) || count($dataExportLaporanPembelianOutlet['rekap']) === 0)
                        <tr>
                            <td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanPembelianOutlet['rekap'] as $LaporanOutlet)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanOutlet['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanOutlet['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPembelianOutlet["chart"])'></canvas>
            </div>
        </div>  

            <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Pembelian Outlet</p>
            </div>
        </div>

        <!-- === Page Break 9 === -->
        <div class="page-break"></div>
        <!-- Export Page 9 -->
        <!-- === Page 9 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanNegosiasi['rekap']) || count($dataExportLaporanNegosiasi['rekap']) === 0)
                        <tr>
                            <td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanNegosiasi['rekap'] as $LaporanNegosiasi)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanNegosiasi['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $LaporanNegosiasi['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanNegosiasi["chart"])'></canvas>
            </div>
        </div>  

            <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Negosiasi</p>
            </div>
        </div>

        <!-- === Export PDF Supports === -->
        <!-- === Page Break 10 === -->
        <div class="page-break"></div>
        <!-- Export Page 10 -->
        <!-- === Page 10 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai Pendapatan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportRekapPendapatanASP['rekap']) || count($dataExportRekapPendapatanASP['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportRekapPendapatanASP['rekap'] as $rekapPendapatanServisASP)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPendapatanServisASP['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPendapatanServisASP['Pelaksana'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPendapatanServisASP['Nilai'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportRekapPendapatanASP["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Supports - Laporan Rekap Pendapatan Servis ASP</p>
        </div>
        </div>

        <!-- === Page Break 11 === -->
        <div class="page-break"></div>
        <!-- Export Page 11 -->
        <!-- === Page 11 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai Piutang (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportRekapPiutangASP['rekap']) || count($dataExportRekapPiutangASP['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportRekapPiutangASP['rekap'] as $rekapPiutangServisASP)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPiutangServisASP['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPiutangServisASP['Pelaksana'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapPiutangServisASP['Nilai'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportRekapPiutangASP["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Supports - Laporan Rekap Piutang Servis ASP</p>
        </div>

        </div>

        <!-- === Page Break 12 === -->
        <div class="page-break"></div>
        <!-- Export Page 12 -->
        <!-- === Page 12 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Pengiriman (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanPengiriman['rekap']) || count($dataLaporanPengiriman['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanPengiriman['rekap'] as $rekapLaporanPengiriman)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapLaporanPengiriman['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapLaporanPengiriman['Pelaksana'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapLaporanPengiriman['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataLaporanPengiriman["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Supports - Laporan Pengiriman</p>
            </div>
        </div>

        <!-- === DIVISI ACCOUNTING === -->

        @foreach ($dataLabaRugi['rekap'] as $item)
        <!-- === Page Break === -->
        <div class="page-break"></div>

        <!-- === Page === -->
        <!--Laba Rugi-->
        <div class="page">
            <!-- Header -->
            <div>
                <img src="{{ asset('images/HEADER.png') }}" alt="">
            </div>

            <!-- Content -->
            <div class="flex justify-center p-6">
                <div class="text-center">
                    <h2 class="text-center font-serif mb-4">Laporan</h2>
                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image"
                        class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Laba Rugi</p>
            </div>
        </div>
        @endforeach

        <!--Neraca-->
        @foreach ($dataNeraca['rekap'] as $item)
        <!-- === Page Break === -->
        <div class="page-break"></div>

        <!-- === Page === -->
        <div class="page">
            <!-- Header -->
            <div>
                <img src="{{ asset('images/HEADER.png') }}" alt="">
            </div>

            <!-- Content -->
            <div class="flex justify-center p-6">
                <div class="text-center">
                    <h2 class="text-center font-serif mb-4">Laporan</h2>
                    <img src="{{ $item['Gambar'] }}" alt="Neraca Image"
                        class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Neraca</p>
            </div>
        </div>
        @endforeach

        <!--Rasio-->
        @foreach ($dataRasio['rekap'] as $item)
        <!-- === Page Break === -->
        <div class="page-break"></div>

        <!-- === Page === -->
        <div class="page">
            <!-- Header -->
            <div>
                <img src="{{ asset('images/HEADER.png') }}" alt="">
            </div>

            <!-- Content -->
            <div class="flex justify-center p-6">
                <div class="text-center">
                    <h2 class="text-center font-serif mb-4">Laporan</h2>
                    <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                        class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Rasio</p>
            </div>
        </div>
        @endforeach

        <!-- === Page Break 16 === -->
        <div class="page-break"></div>
        <!-- Export Page 16 -->
        <!-- === Page 16 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Hutang</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Piutang</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataKHPS['rekap']) || count($dataKHPS['rekap']) === 0)
                        <tr>
                            <td colspan="5" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataKHPS['rekap'] as $KHPS)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $KHPS['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $KHPS['Kas'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $KHPS['Hutang'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $KHPS['Piutang'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $KHPS['Stok'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-pie w-[500px]" id="rekapChart" data-chart='@json($dataKHPS["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Kas Hutang Piutang Stok</p>
        </div>
    </div>

        <!-- === Page Break 17 === -->
        <div class="page-break"></div>
        <!-- Export Page 17 -->
        <!-- === Page 17 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas Masuk</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataArusKas['rekap']) || count($dataArusKas['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataArusKas['rekap'] as $ArusKas)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $ArusKas['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $ArusKas['Masuk'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $ArusKas['Keluar'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-pie w-[500px]" id="rekapChart" data-chart='@json($dataArusKas["chart"])'></canvas>
            </div>
        </div>  

            <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Arus Kas</p>
        </div>
    </div>

    <!--PPN-->
    @foreach ($dataPPn['rekap'] as $item)
    <!-- === Page Break === -->
    <div class="page-break"></div>

    <!-- === Page === -->
    <div class="page">
        <!-- Header -->
        <div>
            <img src="{{ asset('images/HEADER.png') }}" alt="">
        </div>

        <!-- Content -->
        <div class="flex justify-center p-6">
            <div class="text-center">
                <h2 class="text-center font-serif mb-4">Laporan</h2>
                <img src="{{ $item['Gambar'] }}" alt="PPN Image"
                    class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan PPn</p>
        </div>
    </div>
    @endforeach

    <!--TaxPlanning-->
    @foreach ($dataTaxPlanning['rekap'] as $item)
    <!-- === Page Break === -->
    <div class="page-break"></div>

    <!-- === Page === -->
    <div class="page">
        <!-- Header -->
        <div>
            <img src="{{ asset('images/HEADER.png') }}" alt="">
        </div>

        <!-- Content -->
        <div class="flex justify-center p-6">
            <div class="text-center">
                <h2 class="text-center font-serif mb-4">Laporan</h2>
                <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                    class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Tax Planning</p>
        </div>
    </div>
    @endforeach

     <!-- === LAPORAN IT === -->
     @foreach ($dataInstagram['rekap'] as $item)
     <!-- === Page Break === -->
     <div class="page-break"></div>

     <!-- === Page === -->
     <div class="page">
         <!-- Header -->
         <div>
             <img src="{{ asset('images/HEADER.png') }}" alt="">
         </div>

         <!-- Content -->
         <div class="flex justify-center p-6">
             <div class="text-center">
                 <h2 class="text-center font-serif mb-4">Laporan</h2>
                 <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                     class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
             </div>
         </div>

         <!-- Footer -->
         <div class="border-t text-center pt-4 z-50 bg-white">
             <p class="text-sm font-serif mt-2">Laporan IT - Laporan Instagram</p>
         </div>
     </div>
     @endforeach

     @foreach ($dataTiktok['rekap'] as $item)
     <!-- === Page Break === -->
     <div class="page-break"></div>

     <!-- === Page === -->
     <div class="page">
         <!-- Header -->
         <div>
             <img src="{{ asset('images/HEADER.png') }}" alt="">
         </div>

         <!-- Content -->
         <div class="flex justify-center p-6">
             <div class="text-center">
                 <h2 class="text-center font-serif mb-4">Laporan</h2>
                 <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                     class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
             </div>
         </div>

         <!-- Footer -->
         <div class="border-t text-center pt-4 z-50 bg-white">
             <p class="text-sm font-serif mt-2">Laporan IT - Laporan Tiktok</p>
         </div>
     </div>
     @endforeach

     @foreach ($dataBizdev['rekap'] as $item)
     <!-- === Page Break === -->
     <div class="page-break"></div>

     <!-- === Page === -->
     <div class="page">
         <!-- Header -->
         <div>
             <img src="{{ asset('images/HEADER.png') }}" alt="">
         </div>

         <!-- Content -->
         <div class="flex justify-center p-6">
             <div class="text-center">
                 <h2 class="text-center font-serif mb-4">Laporan</h2>
                 <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                     class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
             </div>
         </div>

         <!-- Footer -->
         <div class="border-t text-center pt-4 z-50 bg-white">
             <p class="text-sm font-serif mt-2">Laporan IT - Laporan Bizdev</p>
         </div>
     </div>
     @endforeach

      <div class="page-break"></div>
         <!-- Export Page 17 -->
         <!-- === Page 17 === -->
         <div class="page">
         <!-- buat header disini  -->
         <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-center items-center p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="">
                <h2 class="text-center font-serif items-center">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kendala</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataBizdev1['rekap']) || count($dataBizdev1['rekap']) === 0)
                        <tr>
                            <td colspan="5" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataBizdev1['rekap'] as $bizdev)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $bizdev['Tanggal'] }}</td>
                            <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $bizdev['Kendala'] !!}</td>
                       </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>  
              <!-- Sticky Footer -->
              <div class="border-t text-center pt-4 z-50 bg-white">
             <p class="text-sm font-serif mt-2">Laporan IT - Laporan Bizdev</p>
            </div>
        </div>

    <!-- === Export PDF HRGA === -->
    
            <!-- === Page Break 17 === -->
            <div class="page-break"></div>
            <!-- Export Page 17 -->
            <!-- === Page 17 === -->
            <div class="page">
            <!-- buat header disini  -->
            <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-center items-center p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="">
                <h2 class="text-center font-serif items-center">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pekerjaan</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kondisi Bulan Lalu</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kondisi Bulan Ini</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Update</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Rencana Implementasi</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataPTBOS['rekap']) || count($dataPTBOS['rekap']) === 0)
                        <tr>
                            <td colspan="7  " class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataPTBOS['rekap'] as $Ptbos)
                        <tr>
                            <td class="border border-black p-1 text-center text-[8px] font-serif">{{ $Ptbos['Tanggal'] }}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Pekerjaan'] !!}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Kondisi Bulan Lalu'] !!}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Kondisi Bulan Ini'] !!}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Update'] !!}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Rencana Implementasi'] !!}</td>
                            <td class="border border-black p-1 content-html text-[8px] align-top text-justify font-serif">{!! $Ptbos['Keterangan'] !!}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>  
                <!-- Sticky Footer -->
                <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan PT BOS</p>
            </div>
        </div>

        <!-- === Page Break 17 === -->
        <div class="page-break"></div>
        <!-- Export Page 17 -->
        <!-- === Page 17 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
        <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-center items-center p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="">
                <h2 class="text-center font-serif items-center">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Jam</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Permasalahan</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Impact</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Troubleshooting</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Resolve Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Resolve Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataIJASA['rekap']) || count($dataIJASA['rekap']) === 0)
                        <tr>
                            <td colspan="7" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataIJASA['rekap'] as $IJASA)
                        <tr>
                            <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $IJASA['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $IJASA['Jam'] }}</td>
                            <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $IJASA['Permasalahan'] !!}</td>
                            <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $IJASA['Impact'] !!}</td>
                            <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $IJASA['Troubleshooting'] !!}</td>
                            <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $IJASA['Resolve Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $IJASA['Resolve Jam'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>  
                <!-- Sticky Footer -->
                <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan IJASA</p>
            </div>
        </div>

        @foreach ($dataIJASAGambar['rekap'] as $item)
        <!-- === Page Break === -->
        <div class="page-break"></div>

        <!-- === Page === -->
        <div class="page">
            <!-- Header -->
            <div>
                <img src="{{ asset('images/HEADER.png') }}" alt="">
            </div>

            <!-- Content -->
            <div class="flex justify-center p-6">
                <div class="text-center">
                    <h2 class="text-center font-serif mb-4">Laporan</h2>
                    <img src="{{ $item['Gambar'] }}" alt="Rasio Image"
                        class="h-[500px] w-auto  mx-auto object-contain border border-gray-300">
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Ijasa Gambar</p>
            </div>
        </div>
        @endforeach
   
        <!-- === Page Break 13 === -->
        <div class="page-break"></div>
        <!-- Export Page 13 -->
        <!-- === Page 13 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Sakit (Hari)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanSakit['rekap']) || count($dataLaporanSakit['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanSakit['rekap'] as $rekapSakit)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapSakit['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapSakit['Nama'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapSakit['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-hrga w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataLaporanSakit["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Sakit</p>
        </div>
        </div>

        <!-- === Page Break 14 === -->
        <div class="page-break"></div>
        <!-- Export Page 14 -->
        <!-- === Page 14 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Cuti (Hari)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanCuti['rekap']) || count($dataLaporanCuti['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanCuti['rekap'] as $rekapCuti)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapCuti['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapCuti['Nama'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapCuti['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-hrga w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataLaporanCuti["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Cuti</p>
        </div>
        </div>

        <!-- === Page Break 15 === -->
        <div class="page-break"></div>
        <!-- Export Page 15 -->
        <!-- === Page 15 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Izin (Hari)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanIzin['rekap']) || count($dataLaporanIzin['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanIzin['rekap'] as $rekapIzin)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapIzin['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapIzin['Nama'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapIzin['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-hrga w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataLaporanIzin["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Izin</p>
        </div>
        </div>

        <!-- === Page Break 15 === -->
        <div class="page-break"></div>
        <!-- Export Page 15 -->
        <!-- === Page 15 === -->
        <div class="page">
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2 pr-10">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Terlambat (Hari)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanTerlambat['rekap']) || count($dataLaporanTerlambat['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanTerlambat['rekap'] as $rekapTerlambat)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapTerlambat['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapTerlambat['Nama'] }}</td>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $rekapTerlambat['Total'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-hrga w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataLaporanTerlambat["chart"])'></canvas>
            </div>
        </div>  

        <!-- Sticky Footer -->
        <div class="border-t text-center pt-4 z-50 bg-white">
            <p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Terlambat</p>
        </div>
        </div>

         <!-- === LAPORAN SPI === -->
         <!-- === Page Break 17 === -->
         <div class="page-break"></div>
         <!-- Export Page 17 -->
         <!-- === Page 17 === -->
         <div class="page">
         <!-- buat header disini  -->
         <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-center items-center p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="">
                <h2 class="text-center font-serif items-center">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Aspek</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Masalah</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Solusi</th>
                            <th class="border border-black p-1 text-center text-[10px] font-serif">Implementasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLaporanSPI['rekap']) || count($dataLaporanSPI['rekap']) === 0)
                        <tr>
                            <td colspan="5" class="border border-black p-1 text-center text-[10px] font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLaporanSPI['rekap'] as $SPI)
                        <tr>
                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $SPI['Tanggal'] }}</td>
                            <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPI['Aspek'] !!}</td>
                            <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPI['Masalah'] !!}</td>
                            <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPI['Solusi'] !!}</td>
                            <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPI['Implementasi'] !!}</td>
                       </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>  
              <!-- Sticky Footer -->
              <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan SPI - Laporan SPI Operasional</p>
            </div>
        </div>

         <!-- === Page Break 18 === -->
         <div class="page-break"></div>
         <!-- Export Page 18 -->
         <!-- === Page 18 === -->
         <div class="page">
         <!-- buat header disini  -->
         <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>
 
         <div class="flex justify-top p-6">
             <!-- Tabel Data untuk ekspor PDF -->
             <div style="width: 100%;">
                 <h2 class="text-center font-serif items-center">Tabel Data</h2>
                 <table id="rekapTable" class="dataTable" style="'border-collapse: collapse; width: 100%; font-size: 2px;' border='1'">                     
                    <thead>
                         <tr>
                             <th class="border border-black p-1 text-center font-serif text-[10px]">Tanggal</th>
                             <th class="border border-black p-1 text-center font-serif text-[10px]">Aspek</th>
                             <th class="border border-black p-1 text-center font-serif text-[10px]">Masalah</th>
                             <th class="border border-black p-1 text-center font-serif text-[10px]">Solusi</th>
                             <th class="border border-black p-1 text-center font-serif text-[10px]">Implementasi</th>
                         </tr>
                     </thead>
                     <tbody>
                         @if (empty($dataLaporanSPIIT['rekap']) || count($dataLaporanSPIIT['rekap']) === 0)
                         <tr>
                             <td colspan="5" class="border border-black p-1 text-center text-sm font-serif">Maaf data pada bulan ini tidak ada</td>
                         </tr>
                         @else
                         @foreach($dataLaporanSPIIT['rekap'] as $SPIIT)
                         <tr>
                             <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $SPIIT['Tanggal'] }}</td>
                             <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPIIT['Aspek'] !!}</td>
                             <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPIIT['Masalah'] !!}</td>
                             <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPIIT['Solusi'] !!}</td>
                             <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $SPIIT['Implementasi'] !!}</td>
                         </tr>
                         @endforeach
                         @endif
                     </tbody>
                 </table>
             </div>
         </div>  
 
              <!-- Sticky Footer -->
            <div class="border-t text-center pt-4 z-50 bg-white">
                <p class="text-sm font-serif mt-2">Laporan SPI - Laporan SPI IT</p>
            </div>
         </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>


    //function chart berisikan Rp
    document.addEventListener('DOMContentLoaded', function () {
    const chartCanvases = document.querySelectorAll('.chart-export');

    chartCanvases.forEach((canvas) => {
        const chartData = JSON.parse(canvas.dataset.chart);
        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map((dataset) => ({
                    ...dataset
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 50,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const value = tooltipItem.raw;
                                return tooltipItem.dataset.label + ' : Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
            },
            plugins: [
                {
                    afterDatasetsDraw: function (chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                let textY = bar.y - 10;
                                if (textY < 20) textY = 20;
                                ctx.fillStyle = 'black';
                                ctx.font = 'bold 8px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText('Rp ' + new Intl.NumberFormat('id-ID').format(value), bar.x, textY);
                            });
                        });
                    },
                },
            ],
        });
    });
});

//function chart berisikan Paket
document.addEventListener('DOMContentLoaded', function () {
    const chartCanvases = document.querySelectorAll('.chart-export-paket');

    chartCanvases.forEach((canvas) => {
        const chartData = JSON.parse(canvas.dataset.chart);
        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map((dataset) => ({
                    ...dataset,
                    borderColor: dataset.backgroundColor.map((color) =>
                        color.replace('0.7', '1')
                    ),
                    borderWidth: 1,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 50,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const value = tooltipItem.raw;
                                return tooltipItem.dataset.label + ' : Paket ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Paket ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
            },
            plugins: [
                {
                    afterDatasetsDraw: function (chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                let textY = bar.y - 10;
                                if (textY < 20) textY = 20;
                                ctx.fillStyle = 'black';
                                ctx.font = ' 8px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText( new Intl.NumberFormat('id-ID').format(value) + ' Paket ', bar.x, textY);
                            });
                        });
                    },
                },
            ],
        });
    });
});

//function chart untuk HRGA
document.addEventListener('DOMContentLoaded', function () {
    const chartCanvases = document.querySelectorAll('.chart-export-hrga');

    chartCanvases.forEach((canvas) => {
        const chartData = JSON.parse(canvas.dataset.chart);
        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map((dataset) => ({
                    ...dataset,
                    borderColor: dataset.backgroundColor.map((color) =>
                        color.replace('0.7', '1')
                    ),
                    borderWidth: 1,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 50,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const value = tooltipItem.raw;
                                return tooltipItem.dataset.label + ' : Hari ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: false,
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Hari ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                        },
                    },
                },
            },
            plugins: [
                {
                    afterDatasetsDraw: function (chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                let textY = bar.y - 10;
                                if (textY < 20) textY = 20;
                                ctx.fillStyle = 'black';
                                ctx.font = '8px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText( new Intl.NumberFormat('id-ID').format(value) + ' Hari ', bar.x, textY);
                            });
                        });
                    },
                },
            ],
        });
    });
});

// function chart untuk HRGA
document.addEventListener('DOMContentLoaded', function () {
    const chartCanvases = document.querySelectorAll('.chart-export-pie');

    chartCanvases.forEach((canvas) => {
        let chartData = JSON.parse(canvas.dataset.chart || '{}');

        // Cek jika data kosong, set dummy data
        if (
            !chartData.labels || !chartData.labels.length ||
            !chartData.datasets || !chartData.datasets.length ||
            !chartData.datasets[0].data || !chartData.datasets[0].data.length
        ) {
            chartData = {
                labels: ['Data Kosong'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#e0e0e0'], // Warna abu-abu untuk menunjukkan kekosongan
                }]
            };
        }

        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: chartData,
            options: {
                responsive: true,
                animation: false,
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const maxHeight = 600;
        const pagesContainer = document.getElementById('pagesContainer');
        const originalPage = pagesContainer.querySelector('.page');
        const originalTable = originalPage.querySelector('table');
        const originalRows = Array.from(originalTable.querySelectorAll('tbody tr'));
        const tableHeaderHTML = originalTable.querySelector('thead').outerHTML;
    
        // Bersihkan tbody asli agar tidak dobel saat render ulang
        originalTable.querySelector('tbody').innerHTML = '';
    
        let currentPage = originalPage;
        let currentTbody = currentPage.querySelector('tbody');
    
        for (let i = 0; i < originalRows.length; i++) {
            currentTbody.appendChild(originalRows[i]);
    
            // Cek apakah tinggi tabel melampaui batas
            const tableWrapper = currentPage.querySelector('.table-wrapper');
            if (tableWrapper.scrollHeight > maxHeight) {
                // Pindahkan baris ke halaman baru
                currentTbody.removeChild(originalRows[i]);
    
                // Buat halaman baru
                const newPage = originalPage.cloneNode(true);
                const newTable = newPage.querySelector('table');
                newTable.querySelector('tbody').innerHTML = '';
                newTable.querySelector('thead').outerHTML = tableHeaderHTML;
    
                // Simpan baris ke halaman baru
                newTable.querySelector('tbody').appendChild(originalRows[i]);
    
                // Tambahkan halaman ke kontainer
                pagesContainer.appendChild(newPage);
    
                // Update referensi halaman dan tbody
                currentPage = newPage;
                currentTbody = newPage.querySelector('tbody');
            }
        }
    });
    </script>
    
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const maxHeight = 600;
        const pagesContainer = document.getElementById('pagesContainer');
        const table = document.getElementById('rekapTable');
        const tbody = document.getElementById('rekapTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
    
        let currentPage = pagesContainer.querySelector('.page');
        let content = currentPage.querySelector('.content .table-wrapper tbody');
    
        let newTbody = content;
        let tempTableWrapper = currentPage.querySelector('.table-wrapper');
    
        let newTable = null;
        let newPage = null;
        let headerHtml = table.querySelector('thead').outerHTML;
    
        rows.forEach((row, index) => {
            newTbody.appendChild(row);
    
            // Cek tinggi table-wrapper
            if (tempTableWrapper.scrollHeight > maxHeight) {
                // Hapus row terakhir (karena kelebihan)
                newTbody.removeChild(row);
    
                // Buat page baru
                newPage = document.createElement('div');
                newPage.className = 'page';
    
                newPage.innerHTML = `
                    <div class="header">
                        <img src="{{ asset('images/HEADER.png') }}" alt="Header">
                    </div>
                    <div class="content">
                        <h2 class="text-center font-serif">Tabel Data (lanjutan)</h2>
                        <div class="table-wrapper">
                            <table class="dataTable border border-black">
                                ${headerHtml}
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="footer border-t text-center pt-4 z-50 bg-white">
                        <p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Rekap Penjualan Perusahaan</p>
                    </div>
                `;
    
                pagesContainer.appendChild(newPage);
    
                // Update variabel untuk halaman baru
                currentPage = newPage;
                tempTableWrapper = newPage.querySelector('.table-wrapper');
                newTbody = tempTableWrapper.querySelector('tbody');
    
                // Tambahkan row tadi ke halaman baru
                newTbody.appendChild(row);
            }
        });
    });
    </script> --}}
    
</body>
</html>

