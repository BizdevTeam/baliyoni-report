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
    }
    </style>
    
    </head>
<body onload="window.print()">

    <div id="exportContent">

        <div class="page">
        <!-- Export Page 1 -->
    
        <!-- buat header disini  -->
        <div>
            <img src={{ "images/HEADER.png" }} alt="">
        </div>

        <div class="flex justify-between p-6">
            <!-- Tabel Data untuk ekspor PDF -->
            <div class="width-1/2">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPenjualan['rekap'] as $LaporanPenjualan)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPenjualan['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPenjualan['Total Penjualan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[800px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
            </div>
        </div>

        <!-- footer -->
        <div class="mt-10 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-8">Laporan Marketing - Laporan Penjualan</p>
          </div>

        </div>

        <!-- === Page Break 2 === -->
        <div class="page-break"></div>

        
        <!-- Export Page 2 -->
        <!-- === Page 2 === -->
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
                            <th class="border border-black p-1 text-center text-sm font-sans">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-sans">Perusahaan</th>
                            <th class="border border-black p-1 text-center text-sm">Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPenjualanPerusahaan['rekap'] as $LaporanPenjualanPerusahaan)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Perusahaan'] }}</td>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Total Penjualan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualanPerusahaan["chart"])'></canvas>
            </div>
        </div>

        <!-- footer -->
        <div class="mt-24 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Marketing - Laporan Penjualan</p>
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
            <div class="width-1/2">
                <h2 class="text-center font-serif">Tabel Data</h2>
                <table id="rekapTable" class="dataTable">
                    <thead>
                        <tr>
                            <th class="border border-black p-1 text-center text-sm font-sans">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-sans">Perusahaan</th>
                            <th class="border border-black p-1 text-center text-sm">Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPenjualanPerusahaan['rekap'] as $LaporanPenjualanPerusahaan)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Perusahaan'] }}</td>
                            <td class="border border-black p-1 text-center text-sm">{{ $LaporanPenjualanPerusahaan['Total Penjualan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualanPerusahaan["chart"])'></canvas>
            </div>
        </div>

        <!-- footer -->
        <div class="mt-10 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-8">Laporan Marketing - Laporan Penjualan</p>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
                                ctx.font = 'bold 14px sans-serif';
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
</script>


</script>
</body>
</html>

