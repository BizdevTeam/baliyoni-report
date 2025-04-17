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
</head>
<body onload="window.print()">

    <div id="exportContent">

        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2">
                <h2>Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-sm">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm">Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPenjualan['rekap'] as $LaporanPenjualan)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualan['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualan['Total Penjualan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer" style="margin-top: 20px; height: 300px;">
                <h2>Grafik Laporan</h2>
                <canvas class="w-[600px]" id="rekapChart"></canvas>
            </div>
        </div>

        <!-- footer -->
        <div>
            <p class="text-center text-sm mt-4">Laporan Marketing - Laporan Penjualan</p>
        </div>

        <!-- Tabel Data untuk ekspor PDF -->
        <div>
        <table id="rekapTable" class="dataTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Perusahaan</th>
                    <th>Total Penjualan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataExportLaporanPenjualanPerusahaan as $LaporanPenjualanPerusahaan)
                <tr>
                    <td>{{ $LaporanPenjualanPerusahaan['Tanggal'] }}</td>
                    <td>{{ $LaporanPenjualanPerusahaan['Perusahaan'] }}</td>
                    <td>{{ $LaporanPenjualanPerusahaan['Total Penjualan'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

        <table id="rekapTable" class="dataTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Website</th>
                    <th>Total Paket (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataExportLaporanPaketAdministrasi as $LaporanPaketAdministrasi)
                <tr>
                    <td>{{ $LaporanPaketAdministrasi['Tanggal'] }}</td>
                    <td>{{ $LaporanPaketAdministrasi['Website'] }}</td>
                    <td>{{ $LaporanPaketAdministrasi['Total Paket'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data chart dari controller
            const chartData = @json($dataExportLaporanPenjualan['chart']);

            // Ambil elemen canvas
            const rekapChart = document.getElementById('rekapChart').getContext('2d');

            // Buat chart
            const exportChart = new Chart(rekapChart, {
                type: 'bar'
                , data: {
                    labels: chartData.labels
                    , datasets: chartData.datasets.map(dataset => ({
                        ...dataset
                        , borderColor: dataset.backgroundColor.map(color => color.replace('0.7', '1'))
                        , borderWidth: 1
                    }))
                }
                , options: {
                    responsive: true
                    , maintainAspectRatio: false
                    , plugins: {
                        legend: {
                            display: false, // Sembunyikan legenda
                        }
                    , }
                    , scales: {
                        x: {
                            title: {
                                display: false, // Sembunyikan label sumbu X
                            }
                        , }
                        , y: {
                            beginAtZero: true
                            , 
                            title: {
                        display: false, // Sembunyikan label sumbu Y
                    },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                    , plugins: {
                        title: {
                            display: true
                            , text: 'Grafik Rekap Penjualan'
                        }
                    }
                }
            });
        });

    </script>
</body>
</html>

