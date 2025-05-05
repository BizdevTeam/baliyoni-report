@extends('layouts.app')

@section('content')
<div id="admincontent" class="content-wrapper ml-64 p-4 transition-all duration-300">
    <!-- Grafik Laporan Paket Administrasi -->
    <div class="p-4 ">
        <h1 class="mt-10 text-4xl font-bold text-red-600">Dash<span class="text-red-600">board</span></h1>
    </div>

    <div id="gridContainer" class="grid gap-6 grid-cols-1">
        <!-- Marketing Division -->
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

        <x-floating-popover/>
        <!-- Modal Loader -->
        <div id="loadingModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-red-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                    </path>
                </svg>
                <p class="text-gray-700 text-sm">Mohon tunggu...</p>
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
    </script>

    
    
