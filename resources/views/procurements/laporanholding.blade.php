<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase (Holding) Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    {{-- Aset-aset Anda --}}
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 uh-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Wrapper Alert -->
        @if (session('success') || session('error'))
        <div x-data="{ 
                showSuccess: {{ session('success') ? 'true' : 'false' }},
                showError: {{ session('error') ? 'true' : 'false' }}
             }"
             x-init="setTimeout(() => showSuccess = false, 3000); setTimeout(() => showError = false, 3000);"
             class="fixed top-5 right-5 z-50 flex flex-col gap-3">
    
            <!-- Success Alert -->
            @if (session('success'))
            <div x-show="showSuccess" x-transition.opacity.scale.90
                 class="bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">
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
            <div x-show="showError" x-transition.opacity.scale.90
                 class="bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">
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
        <div id="admincontent" class="mt-14 content-wrapper ml-64 p-4 bg-white duration-300">
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Purchase (Holding) Report</h1>
            
            {{-- Tombol Analisis AI --}}
            @if(empty($aiInsight))
            <div class="my-6 text-center">
                <a href="{{ request()->fullUrlWithQuery(['generate_ai' => 'true']) }}"
                   class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                    Buat Analisis AI
                </a>
            </div>
            @endif

            {{-- Hasil Analisis AI --}}
            @if(!empty($aiInsight))
            <div class="ai-insight mt-4 p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Analisis Penjualan</h3>
                <div class="prose max-w-none">
                    {!! \Illuminate\Support\Str::markdown($aiInsight) !!}
                </div>
            </div>
            @endif
            
            {{-- Kontrol dan Filter --}}
            <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4">
                <!-- Search -->
                <form method="GET" action="{{ route('laporanholding.index') }}" class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                        <input type="month" name="search" placeholder="Search by MM / YYYY" value="{{ request('search') }}"
                               class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                    </div>
                    <button type="submit" class="bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="40" stroke-dashoffset="40" d="M10.76 13.24c-2.34 -2.34 -2.34 -6.14 0 -8.49c2.34 -2.34 6.14 -2.34 8.49 0c2.34 2.34 2.34 6.14 0 8.49c-2.34 2.34 -6.14 2.34 -8.49 0Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="40;0"/></path><path stroke-dasharray="12" stroke-dashoffset="12" d="M10.5 13.5l-7.5 7.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s" values="12;0"/></path></g></svg>
                    </button>
                </form>
                <button class="bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" data-modal-target="#addEventModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M9 14h6"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="8;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M12 11v6"><animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="8;0"/></path></g></svg>
                </button>
                <button id="toggleChartButton" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-4 py-2 rounded shadow-md hover:shadow-lg transition duration-300 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0"/></path><path stroke-dasharray="4" stroke-dashoffset="4" d="M9 17v-3"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="4;0"/></path><path stroke-dasharray="6" stroke-dashoffset="6" d="M12 17v-4"><animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="6;0"/></path><path stroke-dasharray="6" stroke-dashoffset="6" d="M15 17v-5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="1.3s" dur="0.2s" values="6;0"/></path></g></svg>
                </button>
            </div>
    
            <!-- Tabel Data -->
            <div class="overflow-x-auto bg-white shadow-md">
                <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-center">Date</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Company</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Holding Value</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanholdings as $laporanholding)
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanholding->tanggal_formatted }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanholding->perusahaan->nama_perusahaan }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanholding->nilai_formatted }}</td>
                                <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                    <button class="text-red-600 bg-transparent px-3 py-2 rounded" data-modal-target="#editEventModal{{ $laporanholding->id }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form method="POST" action="{{ route('laporanholding.destroy', $laporanholding->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 bg-transparent px-3 py-2 rounded" onclick="return confirm('Are you sure to delete?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 border border-gray-300">No data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Kontrol Paginasi -->
            <div class="flex justify-center items-center mt-2 mb-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <label for="perPage" class="mr-2 text-sm text-gray-600">Show</label>
                    <select id="perPage" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="changePerPage(this.value)">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>12</option>
                        <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                    </select>
                    <span class="ml-2 text-sm text-gray-600">data per page</span>
                </div>
            </div>
            <div class="m-4">
                {{-- Menambahkan withQueryString() untuk menjaga parameter filter saat paginasi --}}
                {{ $laporanholdings->withQueryString()->links('pagination::tailwind') }}
            </div>

            <!-- Chart Container -->
             <div id="formChart" class="visible">
                <div class="flex flex-col mx-auto bg-white p-6 mt-4 rounded-lg shadow-xl border border-grey-500">
                    <h1 class="text-2xl font-bold text-red-600 mb-2 mx-auto font-montserrat text-start">Purchase (Holding) Report Chart</h1>
                    <div class="mt-6 self-center w-full flex justify-center">
                        <div id="chart-wrapper" class="w-full overflow-y-auto overflow-x-hidden">
                            <canvas id="chart"></canvas>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                    <button onclick="exportToPDF()" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <mask id="lineMdCloudAltPrintFilledLoop0">
                                    <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path stroke-dasharray="64" stroke-dashoffset="64" d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                                            <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                                            <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                                        </path>
                                        <g fill="#fff" stroke="none" opacity="0">
                                            <circle cx="12" cy="10" r="6">
                                                <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                                            </circle>
                                            <rect width="9" height="8" x="8" y="12" />
                                            <rect width="15" height="12" x="1" y="8" rx="6">
                                                <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="1;0;1;2;1" />
                                            </rect>
                                            <rect width="13" height="10" x="10" y="10" rx="5">
                                                <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="10;9;10;11;10" />
                                            </rect>
                                            <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                                        </g>
                                        <g fill="#000" fill-opacity="0" stroke="none">
                                            <circle cx="12" cy="10" r="4">
                                                <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                                            </circle>
                                            <rect width="9" height="6" x="8" y="12" />
                                            <rect width="11" height="8" x="3" y="10" rx="4">
                                                <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="3;2;3;4;3" />
                                            </rect>
                                            <rect width="9" height="6" x="12" y="12" rx="3">
                                                <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="12;11;12;13;12" />
                                            </rect>
                                            <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                                            <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                                        </g>
                                        <g stroke="none">
                                            <path fill="#fff" d="M6 11h12v0h-12z">
                                                <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s" values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                                            </path>
                                            <path fill="#000" d="M8 13h8v0h-8z">
                                                <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s" values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                                            </path>
                                            <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                                                <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                                                <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2" repeatCount="indefinite" />
                                            </path>
                                        </g>
                                    </g>
                                </mask>
                                <rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Add Event -->
    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
        <div class="bg-white w-1/2 p-6 rounded shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
            <form method="POST" action="{{ route('laporanholding.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium">Date</label>
                        <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label for="perusahaan_id" class="block text-sm font-medium">Choose Company</label>
                        <select name="perusahaan_id" class="w-full p-2 border rounded" required>
                            @foreach ($perusahaans as $perusahaan)
                                <option value="{{ $perusahaan->id }}">
                                    {{ $perusahaan->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>         
                    </div>     
                    <div>
                        <label for="nilai" class="block text-sm font-medium">Holding Value</label>
                        <input type="number" name="nilai" class="w-full p-2 border rounded" required>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals untuk Edit Event -->
    @foreach ($laporanholdings as $laporanholding)
    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanholding->id }}">
        <div class="bg-white w-1/2 p-6 rounded shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
            <form method="POST" action="{{ route('laporanholding.update', $laporanholding->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium">Date</label>
                        <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $laporanholding->tanggal }}" required>
                    </div>
                    <div>
                        <label for="perusahaan_id" class="block text-sm font-medium">Choose Company</label>
                        <select name="perusahaan_id" class="w-full p-2 border rounded" required>
                            @foreach ($perusahaans as $perusahaan)
                            <option value="{{ $perusahaan->id }}" 
                                {{ $laporanholding->perusahaan_id == $perusahaan->id ? 'selected' : '' }}>
                                {{ $perusahaan->nama_perusahaan }}
                            </option>                                                
                            @endforeach
                        </select>                                                                                  
                    </div>     
                    <div>
                        <label for="nilai" class="block text-sm font-medium">Holding Value</label>
                        <input type="number" name="nilai" class="w-full p-2 border rounded" value="{{ $laporanholding->nilai }}" required>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    //======================================================================
    // LOGIKA UTAMA: SINKRONISASI CHART DENGAN TABEL
    //======================================================================
    let myChart; // Variabel untuk menyimpan instance chart

    // 1. Helper untuk format mata uang
    function formatCurrency(value) {
        if (value >= 1e9) return 'Rp ' + (value / 1e9).toFixed(1).replace('.', ',') + ' M';
        if (value >= 1e6) return 'Rp ' + (value / 1e6).toFixed(1).replace('.', ',') + ' Jt';
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    // Fungsi untuk membuat atau memperbarui chart
    function renderChart() {
        // 2. Baca data langsung dari tabel HTML yang terlihat
        const tableRows = document.querySelectorAll('#data-table tbody tr');
        const labels = [];
        const dataValues = [];
        const backgroundColors = [];

        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 1) {
                const companyName = cells[1].innerText.trim();
                const valueString = cells[2].innerText.trim();
                const numericValue = parseInt(valueString.replace(/[^0-9]/g, ''), 10);

                labels.push(companyName);
                dataValues.push(numericValue);
                backgroundColors.push(`rgba(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, 0.6)`);
            }
        });

        const chartDataFromTable = {
            labels: labels,
            datasets: [{
                label: 'Holding Value',
                data: dataValues,
                backgroundColor: backgroundColors,
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        // 4. Hitung tinggi canvas
        const barThickness = 35;
        const barPadding = 15;
        const perBarHeight = barThickness + barPadding;
        const labelsCount = labels.length > 0 ? labels.length : 1;
        const totalHeight = labelsCount * perBarHeight;

        // [PERBAIKAN] Tetapkan tinggi minimal untuk mencegah bar terlalu tebal saat data sedikit
        const minHeight = 200; // Tinggi minimal dalam piksel, bisa disesuaikan
        const finalHeight = Math.max(totalHeight, minHeight);

        // 3) Apply ke wrapper
        const wrapper = document.getElementById('chart-wrapper');
        wrapper.style.height = finalHeight + 'px';


        const ctx = document.getElementById('chart').getContext('2d');

        // Hancurkan chart lama jika ada, sebelum membuat yang baru
        if (myChart) {
            myChart.destroy();
        }

        // 5. Inisialisasi Chart baru
        myChart = new Chart(ctx, {
            type: 'bar',
            data: chartDataFromTable,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                datasets: { bar: { barThickness: barThickness, categoryPercentage: 1.0, barPercentage: 1.0 }},
                layout: { padding: { left: 20, right: 120, top: 10, bottom: 10 } },
                scales: {
                    x: { beginAtZero: true, ticks: { callback: v => formatCurrency(v) }, grid: { drawBorder: false } },
                    y: { grid: { display: false }, title: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label || ''}: ${formatCurrency(ctx.raw)}` } }
                }
            },
            plugins: [{
                id: 'custom_data_labels_horizontal',
                afterDatasetsDraw(chart) {
                    const { ctx, data } = chart;
                    ctx.save();
                    ctx.font = 'bold 14px sans-serif';
                    ctx.fillStyle = 'black';
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'left';

                    data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (meta.type !== 'bar' || !meta.visible) return;
                        meta.data.forEach((bar, index) => {
                            const value = dataset.data[index];
                            const xPos = bar.x + 8;
                            ctx.fillText('Rp ' + value.toLocaleString('id-ID'), xPos, bar.y);
                        });
                    });
                    ctx.restore();
                }
            }]
        });
    }

    // Panggil fungsi renderChart saat halaman pertama kali dimuat
    renderChart();

    //======================================================================
    // FUNGSI-FUNGSI BANTUAN (MODAL, TOGGLE)
    //======================================================================
    
    document.getElementById('toggleChartButton').addEventListener('click', () => {
        document.getElementById('formChart').classList.toggle('hidden');
    });

    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.querySelector(modalId);
            if (modal) modal.classList.remove('hidden');
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.fixed.z-50').classList.add('hidden');
        });
    });
});

// Fungsi global untuk mengubah item per halaman
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', 1); // Selalu reset ke halaman pertama
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

// Fungsi global untuk ekspor PDF
async function exportToPDF() {
    document.body.style.cursor = 'wait';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan.');
        document.body.style.cursor = 'default';
        return;
    }

    const items = Array.from(document.querySelectorAll('#data-table tbody tr')).map(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length < 3) return null;
        return {
            tanggal: cells[0]?.innerText.trim() || '',
            laporanholding: cells[1]?.innerText.trim() || '',
            nilai: cells[2]?.innerText.trim() || '',
        };
    }).filter(item => item !== null);

    if (items.length === 0) {
        alert('Tidak ada data di tabel untuk diekspor.');
        document.body.style.cursor = 'default';
        return;
    }

    const tableContent = items.map(item => `
        <tr>
            <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
            <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.laporanholding}</td>
            <td style="border: 1px solid #000; padding: 8px; text-align: right;">${item.nilai}</td>
        </tr>
    `).join('');

    const chartCanvas = document.querySelector('#chart');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        document.body.style.cursor = 'default';
        return;
    }
    const chartBase64 = chartCanvas.toDataURL('image/png', 1.0);

    try {
        const response = await fetch('/procurements/laporanholding/export-pdf', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ table: tableContent, chart: chartBase64 }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'laporan_holding.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        } else {
            alert('Gagal mengekspor PDF.');
        }
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('Terjadi kesalahan saat mengekspor PDF.');
    } finally {
        document.body.style.cursor = 'default';
    }
}
</script>
</html>