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
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPenjualan["chart"])'></canvas>
            </div>
        </div>

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Marketing - Laporan Penjualan</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Perusahaan</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Total Penjualan (Rp)</th>
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
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6 ">Laporan Marketing - Laporan Penjualan Perusahaan</p>
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
                                <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                                <th class="border border-black p-1 text-center text-sm font-serif">Website</th>
                                <th class="border border-black p-1 text-center text-sm font-serif">Total Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataExportLaporanPaketAdministrasi['rekap'] as $LaporanPaketAdministrasi)
                            <tr>
                                <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPaketAdministrasi['Tanggal'] }}</td>
                                <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPaketAdministrasi['Website'] }}</td>
                                <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPaketAdministrasi['Total Paket'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Grafik untuk ekspor PDF -->
                <div id="chartContainer">
                    <h2 class="text-center font-serif">Grafik Laporan</h2>
                    <canvas class="chart-export-paket w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPaketAdministrasi["chart"])'></canvas>
                </div>
            </div>  
    
            <!-- footer -->
            <div class="mt-28 border-t items-center align-middle text-center">
                <p class="text-center text-sm font-serif mt-6">Laporan Marketing - Laporan Paket Administrasi</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Status</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Total Paket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportStatusPaket['rekap'] as $StatusPaket)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $StatusPaket['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $StatusPaket['Status'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $StatusPaket['Total Paket'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export-paket w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportStatusPaket["chart"])'></canvas>
            </div>
        </div>  

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Marketing - Laporan Status Paket</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Instansi</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPerInstansi['rekap'] as $LaporanPerInstansi)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPerInstansi['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPerInstansi['Instansi'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanPerInstansi['Nilai'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPerInstansi["chart"])'></canvas>
            </div>
        </div>  

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Marketing - Laporan Per Instansi</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Instansi</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanHolding['rekap'] as $LaporanHolding)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanHolding['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanHolding['Perusahaan'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanHolding['Nilai'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[650px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanHolding["chart"])'></canvas>
            </div>
        </div>  

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Procurements - Laporan Holding</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanStok['rekap'] as $LaporanStok)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanStok['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanStok['Stok'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanStok["chart"])'></canvas>
            </div>
        </div>  

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Procurements - Laporan Stok</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataExportLaporanPembelianOutlet['rekap'] as $LaporanOutlet)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanOutlet['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanOutlet['Total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Grafik untuk ekspor PDF -->
            <div id="chartContainer">
                <h2 class="text-center font-serif">Grafik Laporan</h2>
                <canvas class="chart-export w-[700px] max-h-[500px]" id="rekapChart" data-chart='@json($dataExportLaporanPembelianOutlet["chart"])'></canvas>
            </div>
        </div>  

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Procurements - Laporan Pembelian Outlet</p>
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
                            <th class="border border-black p-1 text-center text-sm font-serif">Tanggal</th>
                            <th class="border border-black p-1 text-center text-sm font-serif">Stok (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($dataExportLaporanNegosiasi['rekap']) || count($dataExportLaporanNegosiasi['rekap']) === 0)
                        <tr>
                            <td colspan="2" class="border border-black p-1 text-center text-sm font-serif">Maaf data pada bulan ini tidak ada</td>
                        </tr>
                        @else
                        @foreach($dataExportLaporanNegosiasi['rekap'] as $LaporanNegosiasi)
                        <tr>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanNegosiasi['Tanggal'] }}</td>
                            <td class="border border-black p-1 text-center text-sm font-serif">{{ $LaporanNegosiasi['Total'] }}</td>
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

        <!-- footer -->
        <div class="mt-28 border-t items-center align-middle text-center">
            <p class="text-center text-sm font-serif mt-6">Laporan Procurements - Laporan Negosiasi</p>
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
                                ctx.font = 'bold 14px sans-serif';
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
</script>


</script>
</body>
</html>

