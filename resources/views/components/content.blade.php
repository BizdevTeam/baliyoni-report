@extends('layouts.app')

@section('content')
@if (Auth::check())
<div class="user-info mb-4">
    <div id="admincontent" class="content-wrapper ml-64 p-4 transition-all duration-300">
        <!-- Grafik Laporan Paket Administrasi -->
        <div class="p-4 ">
            <h1 class="mt-10 text-4xl font-bold text-red-600">Dash<span class="text-red-600">board</span></h1>
        </div>

        <div id="gridContainer" class="grid gap-6 grid-cols-1">
            <!-- MARKETING -->

            <!-- rekap penjualan -->
            @if(in_array(Auth::user()->role, ['superadmin', 'marketing']))
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                Sales Recap Chart
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappenjualan.index") }}" class="text-red-600 font-semibold hover:underline">Sales Recap Chart →</a>
                </div>
            </div>

            <!--rekap penjualan perusahaan-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Sales Recap by Company
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualanPerusahaan["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalLaporanPenjualanPerusahaan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappenjualanperusahaan.index") }}" class="text-red-600 font-semibold hover:underline">Sales Recap by Company →</a>
                </div>
            </div>

            <!--laporan paket administrasi-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Administrative Package Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPaketAdministrasi["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalLaporanPaketAdministrasi["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanpaketadministrasi.index") }}" class="text-red-600 font-semibold hover:underline">Administrative Package Report →</a>
                </div>
            </div>

            <!--status paket-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Package Status Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportStatusPaket["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalStatusPaket["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("statuspaket.index") }}" class="text-red-600 font-semibold hover:underline">Package Status Report →</a>
                </div>
            </div>

            <!-- laporan per instansi -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Institution Based Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPerInstansi["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalInstansi["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanperinstansi.index") }}" class="text-red-600 font-semibold hover:underline">Institution Based Report →</a>
                </div>
            </div>
            @endif
            <!-- END MARKETING -->

            <!-- PROCUREMENT -->
            @if(in_array(Auth::user()->role, ['superadmin', 'procurement']))
            <!-- PROCUREMENT -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Purchase Report (HOLDING)
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanHolding["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalLaporanHolding["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanholding.index") }}" class="text-red-600 font-semibold hover:underline">Purchase Report (HOLDING) →</a>
                </div>
            </div>

            <!--laporan stok-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Stock Report
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanStok["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanstok.index") }}" class="text-red-600 font-semibold hover:underline">Stock Report →</a>
                </div>
            </div>

            <!--laporan pembelian outlet-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Outlet Purchase Report
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanPembelianOutlet["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanoutlet.index") }}" class="text-red-600 font-semibold hover:underline">Outlet Purchase Report →</a>
                </div>
            </div>

            <!--laporan negosiasi -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Negotiation Report
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanNegosiasi["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporannegosiasi.index") }}" class="text-red-600 font-semibold hover:underline">Negotiation Report →</a>
                </div>
            </div>
            @endif
            <!-- END PROCUREMENT -->

            <!-- SUPPORT -->
            @if(in_array(Auth::user()->role, ['superadmin', 'support']))
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    ASP Service Revenue Recap
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportRekapPendapatanASP["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalRekapPendapatanASP["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappendapatanservisasp.index") }}" class="text-red-600 font-semibold hover:underline">ASP Service Revenue Recap →</a>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    ASP Service Receivables Recap
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportRekapPiutangASP["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataTotalRekapPiutangASP["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappiutangservisasp.index") }}" class="text-red-600 font-semibold hover:underline">ASP Service Receivables Recap →</a>
                </div>
            </div>

            <!--laporan pengiriman -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Shipping Report Recap
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataLaporanPengiriman["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporandetrans.index") }}" class="text-red-600 font-semibold hover:underline">Shipping Report Recap →</a>
                </div>
            </div>
            @endif

            <!-- ACCOUNTING: Tampil untuk Superadmin & Accounting -->
            @if(in_array(Auth::user()->role, ['superadmin', 'accounting']))
            <!-- LAPORAN LABA RUGI -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Profit and Loss Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataLabaRugi['rekap']) || count($dataLabaRugi['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataLabaRugi['rekap'] as $LabaRugi)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $LabaRugi['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $LabaRugi['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $LabaRugi['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("labarugi.index") }}" class="text-red-600 font-semibold hover:underline">Profit and Loss Report →</a>
                </div>
            </div>

            <!-- LAPORAN NERACA -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Balance Sheet
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataNeraca['rekap']) || count($dataNeraca['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataNeraca['rekap'] as $Neraca)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Neraca['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $Neraca['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Neraca['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("neraca.index") }}" class="text-red-600 font-semibold hover:underline">Balance Sheet →</a>
                </div>
            </div>

            <!-- LAPORAN RASIO -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Financial Ratio Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataRasio['rekap']) || count($dataRasio['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataRasio['rekap'] as $Rasio)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Rasio['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $Rasio['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Rasio['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rasio.index") }}" class="text-red-600 font-semibold hover:underline">Financial Ratio Report →</a>
                </div>
            </div>

            <!-- LAPORAN KHPS -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Cash, Debts, Receivables, and Stock Reports
                </h1>

                <div class="flex justify-center items-center bg-white shadow-md rounded-lg p-6">
                    <div class="w-[600px]">
                        <canvas class="chart-export-pie" id="rekapChart" data-chart='@json($dataKHPS["chart"])'></canvas>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("khps.index") }}" class="text-red-600 font-semibold hover:underline">Cash, Debts, Receivables, and Stock Reports →</a>
                </div>
            </div>

            <!-- LAPORAN ARUS KAS -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Cash Flow Statement
                </h1>

                <div class="flex justify-center items-center bg-white shadow-md rounded-lg p-6">
                    <div class="w-[600px]">
                        <canvas class="chart-export-pie" id="rekapChart" data-chart='@json($dataArusKas["chart"])'></canvas>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("aruskas.index") }}" class="text-red-600 font-semibold hover:underline">Cash Flow Statement →</a>
                </div>
            </div>

            <!-- LAPORAN PPn -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    PPN Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataPPn['rekap']) || count($dataPPn['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataPPn['rekap'] as $PPn)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $PPn['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $PPn['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $PPn['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanppn.index") }}" class="text-red-600 font-semibold hover:underline">PPN Report →</a>
                </div>
            </div>

            <!-- LAPORAN Tax Planning -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Tax Planning Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataTaxPlanningReport['rekap']) || count($dataTaxPlanningReport['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataTaxPlanningReport['rekap'] as $TaxPlanning)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $TaxPlanning['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $TaxPlanning['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $TaxPlanning['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("taxplaning.index") }}" class="text-red-600 font-semibold hover:underline">Tax Planning Report →</a>
                </div>
            </div>
            <!-- LAPORAN TAX PLANNING -->
                {{-- <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                Tax Planning Report
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas id="tax" data-chart='@json($dataTaxPlanningReport["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("taxplaning.index") }}" class="text-red-600 font-semibold hover:underline">Tax Planning Report →</a>
                </div>
            </div> --}}
            @endif 
            <!-- END ACCOUNTING -->

            <!-- IT -->
            @if(in_array(Auth::user()->role, ['superadmin', 'it']))
            <!-- LAPORAN Instagram -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Instagram Multimedia Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataInstagram['rekap']) || count($dataInstagram['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataInstagram['rekap'] as $Instagram)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Instagram['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $Instagram['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Instagram['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("multimediainstagram.index") }}" class="text-red-600 font-semibold hover:underline">Instagram Multimedia Report →</a>
                </div>
            </div>

            <!-- LAPORAN TIKTOK -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Tiktok Multimedia Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataTiktok['rekap']) || count($dataTiktok['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataTiktok['rekap'] as $Tiktok)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Tiktok['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $Tiktok['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Tiktok['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("tiktok.index") }}" class="text-red-600 font-semibold hover:underline">Tiktok Multimedia Report →</a>
                </div>
            </div>

            <!-- LAPORAN BIZDEV -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Business Development Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataBizdev['rekap']) || count($dataBizdev['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataBizdev['rekap'] as $Bizdev)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Bizdev['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $Bizdev['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $Bizdev['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanbizdevgambar.index") }}" class="text-red-600 font-semibold hover:underline">Business Development Report →</a>
                </div>
            </div>
            @endif
            <!-- END IT -->

            <!-- LAPORAN HRGA -->
            @if(in_array(Auth::user()->role, ['superadmin', 'hrga']))

            <!-- LAPORAN PT BOS -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    PT BOS Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Pekerjaan</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Kondisi Bulan Lalu</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Kondisi Bulan Ini</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Update</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Rencana Implementasi</th>
                                    <th class="border border-black p-1 text-center text-sm font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataPTBOS['rekap']) || count($dataPTBOS['rekap']) === 0)
                                <tr>
                                    <td colspan="7" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
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

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanptbos.index") }}" class="text-red-600 font-semibold hover:underline">PT BOS Report →</a>
                </div>
            </div>

            <!-- LAPORAN IJASA -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    iJASA Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
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
                                    <td colspan="7" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
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

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanijasa.index") }}" class="text-red-600 font-semibold hover:underline">iJASA Report →</a>
                </div>
            </div>

            <!-- LAPORAN iJASA Gambar -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    iJASA Report Picture
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">File</th>
                                    <th class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($dataIJASAGambar['rekap']) || count($dataIJASAGambar['rekap']) === 0)
                                <tr>
                                    <td colspan="3" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                                </tr>
                                @else
                                @foreach($dataIJASAGambar['rekap'] as $ijasaGambar)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $ijasaGambar['Tanggal'] }}</td>
                                    <td class="flex border border-gray-300 px-4 py-2 p-1 items-center justify-center font-serif"><img class="items-center justify-center cursor-pointer h-20 w-20 object-cover block mx-auto" src="{{ $ijasaGambar['Gambar'] }}" alt=""></td>
                                    <td class="border border-gray-300 px-4 py-2 p-1 text-center font-serif">{{ $ijasaGambar['Keterangan'] }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal Gambar -->
                    <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                        <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("ijasagambar.index") }}" class="text-red-600 font-semibold hover:underline">iJASA Report Picture →</a>
                </div>
            </div>

            <!-- LAPORAN Sakit -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Sick Leave Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataLaporanSakit["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataTotalSakit["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporansakit.index") }}" class="text-red-600 font-semibold hover:underline">Sick Leave Report →</a>
                </div>
            </div>

            <!-- LAPORAN CUTI -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Annual Leave Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataLaporanCuti["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataTotalCuti["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporancuti.index") }}" class="text-red-600 font-semibold hover:underline">Annual Leave Report →</a>
                </div>
            </div>

            <!-- LAPORAN Izin -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Permission/Leave Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataLaporanIzin["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataTotalIzin["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanizin.index") }}" class="text-red-600 font-semibold hover:underline">Permission/Leave Report →</a>
                </div>
            </div>

            <!-- LAPORAN Terlambat -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Late Arrival Report
                </h1>
                <div class="mb-2 flex justify-end">
                    <select class="chart-select p-2 border border-gray-300 rounded">
                        <option value="chart1">Chart Biasa</option>
                        <option value="chart2">Chart Total</option>
                    </select>
                </div>

                <div class="chart-container chart1 bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataLaporanTerlambat["chart"])'></canvas>
                </div>
                <!--ganti source datanya nanti-->
                <div class="chart-container chart2 bg-white shadow-md rounded-lg p-6 hidden">
                    <canvas class="chart-export-hrga w-full h-96" data-chart='@json($dataTotalTerlambat["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanizin.index") }}" class="text-red-600 font-semibold hover:underline">Late Arrival Report →</a>
                </div>
            </div>
            @endif

            <!-- SPI: Tampil untuk Superadmin & SPI -->
            @if(in_array(Auth::user()->role, ['superadmin', 'spi']))
            <!-- SPI -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Operational SPI Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
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
                                    <td colspan="5" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
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

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanspi.index") }}" class="text-red-600 font-semibold hover:underline">Operational SPI Report →</a>
                </div>
            </div>
            <!-- SPI -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    IT SPI Report
                </h1>

                <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                    <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto">
                        <!-- Container pembatas dan scroll -->
                        <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
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
                                @if (empty($dataLaporanSPIIT['rekap']) || count($dataLaporanSPIIT['rekap']) === 0)
                                <tr>
                                    <td colspan="5" class="border border-gray-300 p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
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

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanspi.index") }}" class="text-red-600 font-semibold hover:underline">IT SPI Report →</a>
                </div>
            </div>
            @endif
        </div>
        @endif

        <x-floating-popover />

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Event listener for enlarging the image on click
                $(".cursor-pointer").on("click", function(e) {
                    let imgSrc = $(this).attr("src");
                    $("#modalImage").attr("src", imgSrc);
                    $("#imageModal").fadeIn();
                });

                // Close modal when clicking outside the image
                $("#imageModal").on("click", function(e) {
                    if (!$(e.target).is("#modalImage")) {
                        $("#imageModal").fadeOut();
                    }
                });
            });

        </script>
        <script>
            //function chart berisikan Rp
            document.addEventListener('DOMContentLoaded', function() {
                const chartCanvases = document.querySelectorAll('.chart-export');

                chartCanvases.forEach((canvas) => {
                    const chartData = JSON.parse(canvas.dataset.chart);
                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar'
                        , data: {
                            labels: chartData.labels
                            , datasets: chartData.datasets.map((dataset) => ({
                                ...dataset
                            }))
                        , }
                        , options: {
                            responsive: true
                            , maintainAspectRatio: false
                            , animation: false
                            , transitions: {
                                active: {
                                    animation: {
                                        duration: 0
                                    }
                                }
                            }
                            , layout: {
                                padding: {
                                    top: 50
                                , }
                            , }
                            , plugins: {
                                legend: {
                                    display: false
                                , }
                                , title: {
                                    display: false
                                , }
                                , tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const value = tooltipItem.raw;
                                            return tooltipItem.dataset.label + ' : Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                            , scales: {
                                x: {
                                    title: {
                                        display: false
                                    , }
                                , }
                                , y: {
                                    beginAtZero: true
                                    , title: {
                                        display: false
                                    , }
                                    , ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                        , }
                        , plugins: [{
                            afterDatasetsDraw: function(chart) {
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
                            }
                        , }, ]
                    , });
                });
            });

            //function chart berisikan Paket
            document.addEventListener('DOMContentLoaded', function() {
                const chartCanvases = document.querySelectorAll('.chart-export-paket');

                chartCanvases.forEach((canvas) => {
                    const chartData = JSON.parse(canvas.dataset.chart);
                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar'
                        , data: {
                            labels: chartData.labels
                            , datasets: chartData.datasets.map((dataset) => ({
                                ...dataset
                                , borderColor: dataset.backgroundColor.map((color) =>
                                    color.replace('0.7', '1')
                                )
                                , borderWidth: 1
                            , }))
                        , }
                        , options: {
                            responsive: true
                            , maintainAspectRatio: false
                            , animation: false
                            , transitions: {
                                active: {
                                    animation: {
                                        duration: 0
                                    }
                                }
                            }
                            , layout: {
                                padding: {
                                    top: 50
                                , }
                            , }
                            , plugins: {
                                legend: {
                                    display: false
                                , }
                                , title: {
                                    display: false
                                , }
                                , tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const value = tooltipItem.raw;
                                            return tooltipItem.dataset.label + ' : Paket ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                            , scales: {
                                x: {
                                    title: {
                                        display: false
                                    , }
                                , }
                                , y: {
                                    beginAtZero: true
                                    , title: {
                                        display: false
                                    , }
                                    , ticks: {
                                        callback: function(value) {
                                            return 'Paket ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                        , }
                        , plugins: [{
                            afterDatasetsDraw: function(chart) {
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
                                        ctx.fillText(new Intl.NumberFormat('id-ID').format(value) + ' Paket ', bar.x, textY);
                                    });
                                });
                            }
                        , }, ]
                    , });
                });
            });

            //function chart untuk HRGA
            document.addEventListener('DOMContentLoaded', function() {
                const chartCanvases = document.querySelectorAll('.chart-export-hrga');

                chartCanvases.forEach((canvas) => {
                    const chartData = JSON.parse(canvas.dataset.chart);
                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar'
                        , data: {
                            labels: chartData.labels
                            , datasets: chartData.datasets.map((dataset) => ({
                                ...dataset
                                , borderColor: dataset.backgroundColor.map((color) =>
                                    color.replace('0.7', '1')
                                )
                                , borderWidth: 1
                            , }))
                        , }
                        , options: {
                            responsive: true
                            , maintainAspectRatio: false
                            , animation: false
                            , transitions: {
                                active: {
                                    animation: {
                                        duration: 0
                                    }
                                }
                            }
                            , layout: {
                                padding: {
                                    top: 50
                                , }
                            , }
                            , plugins: {
                                legend: {
                                    display: false
                                , }
                                , title: {
                                    display: false
                                , }
                                , tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const value = tooltipItem.raw;
                                            return tooltipItem.dataset.label + ' : Hari ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                            , scales: {
                                x: {
                                    title: {
                                        display: false
                                    , }
                                , }
                                , y: {
                                    beginAtZero: true
                                    , title: {
                                        display: false
                                    , }
                                    , ticks: {
                                        callback: function(value) {
                                            return 'Hari ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    , }
                                , }
                            , }
                        , }
                        , plugins: [{
                            afterDatasetsDraw: function(chart) {
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
                                        ctx.fillText(new Intl.NumberFormat('id-ID').format(value) + ' Hari ', bar.x, textY);
                                    });
                                });
                            }
                        , }, ]
                    , });
                });
            });

            // function chart untuk HRGA
            document.addEventListener('DOMContentLoaded', function() {
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
                            labels: ['Data Kosong']
                            , datasets: [{
                                data: [1]
                                , backgroundColor: ['#e0e0e0'], // Warna abu-abu untuk menunjukkan kekosongan
                            }]
                        };
                    }

                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'pie'
                        , data: chartData
                        , options: {
                            responsive: true
                            , animation: false
                            , transitions: {
                                active: {
                                    animation: {
                                        duration: 0
                                    }
                                }
                            }
                            , plugins: {
                                legend: {
                                    position: 'top'
                                , }
                                , tooltip: {
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

        {{-- <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".chart-group").forEach(group => {
                    const select = group.querySelector(".chart-select");
                    const charts = group.querySelectorAll(".chart-container");

                    select.addEventListener("change", function() {
                        const selectedValue = this.value;

                        charts.forEach(chart => {
                            chart.classList.toggle("hidden", !chart.classList.contains(selectedValue));
                        });
                    });
                });
            });

            var chartData = @json($dataTaxPlanningReport["chart"]);

            var ctx = document.getElementById('tax').getContext('2d');

            var barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets,
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true, // tampilkan legenda karena ada 2 dataset
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return context.dataset.label + ' : Rp ' + value.toLocaleString();
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: false,
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 45,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                },
                            },
                        },
                    },
                    layout: {
                        padding: {
                            top: 50,
                        },
                    },
                },
                plugins: [{
                    id: 'customDataLabels',
                    afterDatasetsDraw(chart) {
                        const {ctx} = chart;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                let textY = bar.y - 10;
                                if (textY < 20) textY = 20;
                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.font = 'bold 12px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText('Rp ' + value.toLocaleString(), bar.x, textY);
                                ctx.restore();
                            });
                        });
                    }
                }]
            });

        </script> --}}


        <!-- Modal Loader -->
        <script>
            let loadingStartTime;

            function showLoading() {
                loadingStartTime = Date.now();
                document.getElementById('loadingModal').classList.remove('hidden');
            }

            function hideLoading(minDuration = 2000) { // default 1000ms (1 detik)
                const elapsed = Date.now() - loadingStartTime;
                const remaining = Math.max(minDuration - elapsed, 0);

                setTimeout(() => {
                    document.getElementById('loadingModal').classList.add('hidden');
                }, remaining);
            }

            // Saat DOM siap, tampilkan loading
            document.addEventListener("DOMContentLoaded", () => {
                showLoading();

                // Sembunyikan setelah halaman benar-benar selesai load
                window.addEventListener('load', () => {
                    hideLoading(); // akan disembunyikan minimal 1 detik setelah muncul
                });
            });

            // Add this to your resources/js/app.js or create a new file
            document.addEventListener('DOMContentLoaded', function() {
                // Check if Tailwind CSS is loaded
                function isTailwindLoaded() {
                    const testElement = document.createElement('div');
                    testElement.className = 'hidden sm:block'; // Tailwind class for testing
                    document.body.appendChild(testElement);

                    const isLoaded = getComputedStyle(testElement).display === 'none';
                    document.body.removeChild(testElement);

                    return isLoaded;
                }

                // Function to reveal content once CSS is loaded
                function revealContent() {
                    // Remove preload class to show content
                    document.documentElement.classList.remove('preload');
                }

                // Try to detect if Tailwind is loaded
                if (isTailwindLoaded()) {
                    revealContent();
                } else {
                    // If not loaded yet, wait a bit and try again
                    setTimeout(function() {
                        revealContent();
                    }, 300); // Adjust timeout as needed
                }

                // In case everything else fails, reveal the content after a maximum timeout
                setTimeout(revealContent, 1000);
            });

        </script>

