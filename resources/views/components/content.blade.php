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
                    Grafik Laporan Rekap Penjualan
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappenjualan.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Penjualan →</a>
                </div>
            </div>
            <!--rekap penjualan perusahaan-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Rekap Penjualan Perusahaan
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappenjualanperusahaan.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Penjualan →</a>
                </div>
            </div>

            <!--laporan paket administrasi-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Paket Administrasi
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanpaketadministrasi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Paket Administrasi →</a>
                </div>
            </div>

            <!--status paket-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Status Paket
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("statuspaket.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Status Paket →</a>
                </div>
            </div>

            <!-- laporan per instansi -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Per Instansi
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanperinstansi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Per Instansi →</a>
                </div>
            </div>
            @endif
            <!-- END MARKETING -->

            <!-- PROCUREMENT -->
            @if(in_array(Auth::user()->role, ['superadmin', 'procurement']))
            <!-- PROCUREMENT -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Rekap Pembelian (HOLDING)
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanholding.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Pembelian (HOLDING) →</a>
                </div>
            </div>

            <!--laporan stok-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Stok
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanStok["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanstok.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Stok →</a>
                </div>
            </div>

            <!--laporan pembelian outlet-->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Pembelian Outlet
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanPembelianOutlet["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanoutlet.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Pembelian Outlet →</a>
                </div>
            </div>

            <!--laporan negosiasi -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Negosiasi
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataExportLaporanNegosiasi["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporannegosiasi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Negosiasi →</a>
                </div>
            </div>
            @endif
            <!-- END PROCUREMENT -->

            <!-- SUPPORT -->
            @if(in_array(Auth::user()->role, ['superadmin', 'support']))
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Rekap Pendapatan Servis ASP
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappendapatanservisasp.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Pendapatan Servis ASP →</a>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300 chart-group" data-group="penjualan-perusahaan">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Rekap Piutang Servis ASP
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
                    <canvas class="chart-export w-full h-96" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("rekappiutangservisasp.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Piutang Servis ASP →</a>
                </div>
            </div>

            <!--laporan pengiriman -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
                <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                    Grafik Laporan Rekap Pengiriman
                </h1>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas class="chart-export w-full h-96" id="rekapChart" data-chart='@json($dataLaporanPengiriman["chart"])'></canvas>
                </div>

                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporandetrans.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman→ →</a>
                </div>
            </div>
            @endif
            
        <!-- ACCOUNTING: Tampil untuk Superadmin & Accounting -->
        @if(in_array(Auth::user()->role, ['superadmin', 'accounting']))
        <!-- LAPORAN LABA RUGI -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
                Laporan Laba Rugi
            </h1>
            
            <div class="flex bg-white shadow-md rounded-lg p-6 justify-center">
                <div class="w-full items-center md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                <table id="rekapTable" class="dataTable table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center font-serif">File</th>
                            <th class="border border-black p-1 text-center font-serif">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataLabaRugi['rekap']) || count($dataLabaRugi['rekap']) === 0)
                        <tr>
                            <td colspan="3" class="border border-black p-1 text-center font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataLabaRugi['rekap'] as $LabaRugi)
                        <tr>
                            <td class="border border-black p-1 text-center font-serif">{{ $LabaRugi['Tanggal'] }}</td>
                            <td class="flex border border-black p-1 items-center justify-center font-serif"><img class="items-center justify-center h-60 w-60 object-cover" src="{{ $LabaRugi['Gambar'] }}" alt=""></td>
                            <td class="border border-black p-1 text-center font-serif">{{ $LabaRugi['Keterangan'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            </div>

            <div class="flex justify-end mt-4">
                    <a href="{{ route("labarugi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Laba Rugi →</a>
                </div>
            </div>

        @endif





        </div>
        @endif






        <x-floating-popover />

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <script>
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

        </script>


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

