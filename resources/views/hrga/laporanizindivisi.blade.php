<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Permission/Leave Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
    <style>
        .content-wrapper { margin-left: 16rem; }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Wrapper Alert -->
        @if (session('success') || session('error'))
        <div x-data="{ 
            showSuccess: {{ session('success') ? 'true' : 'false' }},
            showError: {{ session('error') ? 'true' : 'false' }}
         }" x-init="setTimeout(() => showSuccess = false, 3000); setTimeout(() => showError = false, 3000);" class="fixed top-5 right-5 z-50 flex flex-col gap-3">

            <!-- Success Alert -->
            @if (session('success'))
            <div x-show="showSuccess" x-transition.opacity.scale.90 class="bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-full max-w-md">
                <span class="text-2xl">✅</span>
                <div>
                    <h3 class="font-bold">Success!</h3>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button @click="showSuccess = false" class="ml-auto text-white text-lg font-bold">&times;</button>
            </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
            <div x-show="showError" x-transition.opacity.scale.90 class="bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-full max-w-md">
                <span class="text-2xl">⚠️</span>
                <div>
                    <h3 class="font-bold">Error!</h3>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
                <button @click="showError = false" class="ml-auto text-white text-lg font-bold">&times;</button>
            </div>
            @endif
        </div>
        @endif

        <!-- Main Content -->
        <div id="admincontent" class="mt-14 content-wrapper p-4 bg-gray-50 duration-300">
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Permission/Leave Report</h1>
            <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4">
                <!-- Search -->
                <form method="GET" action="{{ route('laporanizindivisi.index') }}" class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                        <input type="month" name="search" placeholder="Search by MM / YYYY" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                    </div>
                    <button type="submit" class="bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <path stroke-dasharray="40" stroke-dashoffset="40" d="M10.76 13.24c-2.34 -2.34 -2.34 -6.14 0 -8.49c2.34 -2.34 6.14 -2.34 8.49 0c2.34 2.34 2.34 6.14 0 8.49c-2.34 2.34 -6.14 2.34 -8.49 0Z">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="40;0" />
                                </path>
                                <path stroke-dasharray="12" stroke-dashoffset="12" d="M10.5 13.5l-7.5 7.5">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s" values="12;0" />
                                </path>
                            </g>
                        </svg>
                    </button>
                </form>
                <button class="bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" data-modal-target="#addEventModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                            </path>
                            <path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0" />
                            </path>
                            <path stroke-dasharray="8" stroke-dashoffset="8" d="M9 14h6">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="8;0" />
                            </path>
                            <path stroke-dasharray="8" stroke-dashoffset="8" d="M12 11v6">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="8;0" />
                            </path>
                        </g>
                    </svg>
                </button>
                <button id="toggleFormButton" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-4 py-2 rounded shadow-md hover:shadow-lg transition duration-300 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path stroke-dasharray="64" stroke-dashoffset="64" d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                            </path>
                            <path stroke-dasharray="6" stroke-dashoffset="6" d="M12 14l-3 -3M12 14l3 -3">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.3s" values="6;0" />
                            </path>
                        </g>
                    </svg>
                </button>
                <button id="toggleChartButton" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-4 py-2 rounded shadow-md hover:shadow-lg transition duration-300 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                            </path>
                            <path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0" />
                            </path>
                            <path stroke-dasharray="4" stroke-dashoffset="4" d="M9 17v-3">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="4;0" />
                            </path>
                            <path stroke-dasharray="6" stroke-dashoffset="6" d="M12 17v-4">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="6;0" />
                            </path>
                            <path stroke-dasharray="6" stroke-dashoffset="6" d="M15 17v-5">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.3s" dur="0.2s" values="6;0" />
                            </path>
                        </g>
                    </svg>
                </button>
            </div>


            <div id="formContainer" class="hidden">
                <div class="mx-auto bg-white p-6 rounded-lg shadow">
                    <div class="overflow-x-auto bg-white shadow-md">
                        <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Divisi</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Total Terlambat</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporanizindivisis as $laporanizindivisi)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanizindivisi->tanggal_formatted }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanizindivisi->divisi }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanizindivisi->total_izin }}</td>
                                    <td class="border border-gray-300 py-2 text-center flex justify-center gap-2">
                                        <button class="bg-transparent text-blue-600 px-3 py-2 rounded" data-modal-target="#editEventModal{{ $laporanizindivisi->id_laporan_izin_divisi }}"><i class="fa fa-pen"></i></button>
                                        <form method="POST" action="{{ route('laporanizindivisi.destroy', $laporanizindivisi->id_laporan_izin_divisi) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-transparent text-red-600 px-3 py-2 rounded"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between items-center mt-4">
                        <div class="flex items-center mx-auto">
                            <label for="perPage" class="mr-2 text-sm text-gray-600">Tampilkan</label>
                            <select id="perPage" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                            </select>
                            <span class="ml-2 text-sm text-gray-600">data per halaman</span>
                        </div>
                        <div>
                            {{ $laporanizindivisis->withQueryString()->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div id="formChart" class="visible">
                <div class="flex flex-col mx-auto bg-white p-6 mt-4 rounded-lg shadow-xl border border-grey-500 chart-group">
                    <div class="mb-4 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-red-600 font-montserrat mx-auto">Permission/Leave Report Chart</h1>
                        <select class="chart-select p-2 border border-gray-300 rounded">
                            <option value="chartBiasa">Chart Biasa</option>
                            <option value="chartTotal">Chart Total</option>

                        </select>
                    </div>

                    <div class="mt-6 self-center w-full relative" style="height: 450px;">
                        <div class="chart-container chartBiasa hidden w-full h-full">
                            <canvas id="chartBiasa" class="chart-canvas" data-axis="y" data-unit="hari"></canvas>
                        </div>
                        <div class="chart-container chartTotal w-full h-full">
                            <canvas id="chartTotal" class="chart-canvas" data-axis="y" data-unit="hari"></canvas>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button id="exportPdfButton" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><mask id="lineMdCloudAltPrintFilledLoop0"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" /><set fill="freeze" attributeName="opacity" begin="0.7s" to="0" /></path><g fill="#fff" stroke="none" opacity="0"><circle cx="12" cy="10" r="6"><animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" /></circle><rect width="9" height="8" x="8" y="12" /><rect width="15" height="12" x="1" y="8" rx="6"><animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="1;0;1;2;1" /></rect><rect width="13" height="10" x="10" y="10" rx="5"><animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="10;9;10;11;10" /></rect><set fill="freeze" attributeName="opacity" begin="0.7s" to="1" /></g><g fill="#000" fill-opacity="0" stroke="none"><circle cx="12" cy="10" r="4"><animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" /></circle><rect width="9" height="6" x="8" y="12" /><rect width="11" height="8" x="3" y="10" rx="4"><animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="3;2;3;4;3" /></rect><rect width="9" height="6" x="12" y="12" rx="3"><animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="12;11;12;13;12" /></rect><set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" /><animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" /></g><g stroke="none"><path fill="#fff" d="M6 11h12v0h-12z"><animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s" values="M6 11h12v0h-12z;M6 11h12v11h-12z" /></path><path fill="#000" d="M8 13h8v0h-8z"><animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s" values="M8 13h8v0h-8z;M8 13h8v7h-8z" /></path><path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z"><animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" /><animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2" repeatCount="indefinite" /></path></g></g></mask><rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" /></svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Modals -->
            @foreach ($laporanizindivisis as $laporanizindivisi)
            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanizindivisi->id_laporan_izin_divisi }}">
                <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                    <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                    <form method="POST" action="{{ route('laporanizindivisi.update', $laporanizindivisi->id_laporan_izin_divisi) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium">Date</label>
                                <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $laporanizindivisi->tanggal }}" required>
                            </div>
                            <div>
                                <label for="divisi" class="block text-sm font-medium">Division</label>
                                <select name="divisi" class="w-full p-2 border rounded" required>
                                    <option value="Marketing" {{ $laporanizindivisi->divisi == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="Procurement" {{ $laporanizindivisi->divisi == 'Procurement' ? 'selected' : '' }}>Procurement</option>
                                    <option value="Accounting" {{ $laporanizindivisi->divisi == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                    <option value="IT" {{ $laporanizindivisi->divisi == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="HRGA" {{ $laporanizindivisi->divisi == 'HRGA' ? 'selected' : '' }}>HRGA</option>
                                    <option value="Support" {{ $laporanizindivisi->divisi == 'Support' ? 'selected' : '' }}>Support</option>
                                    <option value="SPI" {{ $laporanizindivisi->divisi == 'SPI' ? 'selected' : '' }}>SPI</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_izin" class="block text-sm font-medium">Permission Leave Total</label>
                                <input type="number" name="total_izin" class="w-full p-2 border rounded" value="{{ $laporanizindivisi->total_izin }}" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
                <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                    <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
                    <form method="POST" action="{{ route('laporanizindivisi.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium">Date</label>
                                <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label for="divisi" class="block text-sm font-medium">Division</label>
                                <select name="divisi" class="w-full p-2 border rounded" required>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Procurement">Procurement</option>
                                    <option value="Accounting">Accounting</option>
                                    <option value="IT">IT</option>
                                    <option value="HRGA">HRGA</option>
                                    <option value="Support">Support</option>
                                    <option value="SPI">SPI</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_izin" class="block text-sm font-medium">Permission Leave Total</label>
                                <input type="number" name="total_izin" class="w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- VARIABLE DECLARATIONS ---
    const chartInstances = new Map();
    
    // --- FUNCTION DEFINITIONS ---

    /**
     * Generates a random RGBA color string.
     * @param {number} opacity The opacity of the color (0 to 1).
     * @returns {string} An RGBA color string.
     */
    function getRandomRGBA(opacity = 0.7) {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    /**
     * Chart.js plugin to display data labels on top of the bars.
     */
    const dataLabelsPlugin = {
        id: 'customDataLabels',
        afterDatasetsDraw(chart) {
            const { ctx, data, config } = chart;
            if (config.type !== 'bar') return;

            ctx.save();
            ctx.font = 'bold 11px Arial';
            const axis = config.options.indexAxis;

            data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach((element, index) => {
                        const value = dataset.data[index];
                        const unit = chart.canvas.dataset.unit || '';
                        const labelText = `${value} ${unit}`;

                        if (axis === 'y') { // Horizontal bars
                            ctx.textAlign = 'left';
                            ctx.textBaseline = 'middle';
                            let xPos = element.x + 5;
                            ctx.fillStyle = '#333';
                            if (element.x + ctx.measureText(labelText).width + 10 > chart.chartArea.right) {
                                xPos = element.x - 5;
                                ctx.textAlign = 'right';
                                ctx.fillStyle = 'white';
                            }
                            ctx.fillText(labelText, xPos, element.y);
                        } else { // Vertical bars
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.fillStyle = '#333';
                            ctx.fillText(labelText, element.x, element.y - 5);
                        }
                    });
                }
            });
            ctx.restore();
        }
    };
    Chart.register(dataLabelsPlugin);

    /**
     * Creates or updates a chart instance on a given canvas.
     * @param {HTMLCanvasElement} canvas - The canvas element to draw the chart on.
     * @param {object} chartData - The data object for the chart.
     */
    function createOrUpdateChart(canvas, chartData) {
        if (!canvas) return;
        
        const axis = canvas.dataset.axis || 'x';
        const unit = canvas.dataset.unit || '';
        const config = {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: axis,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.dataset.label || ''}: ${context.raw} ${unit}`
                        }
                    }
                }
            }
        };

        if (chartInstances.has(canvas)) chartInstances.get(canvas).destroy();
        chartInstances.set(canvas, new Chart(canvas.getContext('2d'), config));
    }

    /**
     * Reads data from the visible HTML table and generates data for both chart types.
     */
    function renderChartsFromTable() {
        const tableRows = document.querySelectorAll('#data-table tbody tr');
        
        const labelsBiasa = [];
        const dataBiasa = [];
        const aggregatedData = {};

        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 3) return; 

            const date = cells[0].innerText.trim();
            const division = cells[1].innerText.trim();
            const sickDays = parseInt(cells[2].innerText.trim(), 10);

            if (!isNaN(sickDays)) {
                // Data for detailed chart
                labelsBiasa.push(`${division} (${date})`);
                dataBiasa.push(sickDays);

                // Aggregate data for total chart
                aggregatedData[division] = (aggregatedData[division] || 0) + sickDays;
            }
        });

        // Prepare data for "Chart Biasa"
        const chartDataBiasa = {
            labels: labelsBiasa,
            datasets: [{
                label: 'Total Terlambat',
                data: dataBiasa,
                backgroundColor: labelsBiasa.map(() => getRandomRGBA()),
            }]
        };

        // Prepare data for "Chart Total"
        const labelsTotal = Object.keys(aggregatedData);
        const dataTotal = Object.values(aggregatedData);
        const chartDataTotal = {
            labels: labelsTotal,
            datasets: [{
                label: 'Total Terlambat',
                data: dataTotal,
                backgroundColor: labelsTotal.map(() => getRandomRGBA()),
            }]
        };
        
        createOrUpdateChart(document.getElementById('chartBiasa'), chartDataBiasa);
        createOrUpdateChart(document.getElementById('chartTotal'), chartDataTotal);
    }

    // --- EVENT LISTENERS & INITIALIZATION ---

    // Initial chart render
    renderChartsFromTable();

    // Setup chart switcher
    document.querySelector('.chart-select')?.addEventListener('change', function() {
        document.querySelectorAll('.chart-container').forEach(container => {
            container.classList.toggle('hidden', !container.classList.contains(this.value));
        });
        // Resize the newly visible chart to ensure it renders correctly
        const visibleCanvas = document.querySelector(`.chart-container.${this.value} .chart-canvas`);
        if (visibleCanvas && chartInstances.has(visibleCanvas)) {
            setTimeout(() => chartInstances.get(visibleCanvas).resize(), 50);
        }
    });
    // Trigger change to set initial view
    document.querySelector('.chart-select')?.dispatchEvent(new Event('change'));

    // Setup UI toggles
    document.getElementById('toggleFormButton')?.addEventListener('click', () => document.getElementById('formContainer')?.classList.toggle('hidden'));
    document.getElementById('toggleChartButton')?.addEventListener('click', () => document.getElementById('formChart')?.classList.toggle('hidden'));
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.querySelector(this.getAttribute('data-modal-target'));
            if (modal) modal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.fixed.z-50').classList.add('hidden');
        });
    });
    // Setup pagination changer
    document.getElementById('perPage')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.set('page', 1); // Reset to page 1 when changing items per page
        window.location.href = url.toString();
    });

    // Setup PDF export button
    document.getElementById('exportPdfButton')?.addEventListener('click', async () => {
        document.body.style.cursor = 'wait';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            alert('CSRF token not found.');
            document.body.style.cursor = 'default';
            return;
        }

        const tableContent = Array.from(document.querySelectorAll('#data-table tbody tr')).map(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 3) return '';
            return `<tr>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${cells[0].innerText}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">${cells[1].innerText}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${cells[2].innerText}</td>
                    </tr>`;
        }).join('');

        const visibleChartContainer = document.querySelector('.chart-container:not(.hidden)');
        if (!visibleChartContainer) {
            alert('No visible chart to export.');
            document.body.style.cursor = 'default';
            return;
        }
        const chartBase64 = visibleChartContainer.querySelector('canvas').toDataURL('image/png', 1.0);

        try {
            const response = await fetch('/hrga/laporanizindivisi/export-pdf', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/pdf'
                },
                body: JSON.stringify({ table: tableContent, chart: chartBase64 }),
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'permission-leave-report.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } else {
                alert('Failed to export PDF.');
            }
        } catch (error) {
            console.error('Error exporting to PDF:', error);
        } finally {
            document.body.style.cursor = 'default';
        }
    });
});
</script>
</html>
