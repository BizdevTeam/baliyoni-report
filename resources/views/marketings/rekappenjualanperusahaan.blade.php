<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales Recap by Company</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Wrapper Alert -->
        @if (session('success') || session('error'))
        <div x-data="{ 
            showSuccess: {{ session('success') ? 'true' : 'false' }},
            showError: {{ session('error') ? 'true' : 'false' }}
        }" x-init="setTimeout(() => showSuccess = false, 5000); setTimeout(() => showError = false, 5000);" class="fixed top-5 right-5 z-50 flex flex-col gap-3">

            <!-- Success Alert -->
            @if (session('success'))
            <div x-show="showSuccess" x-transition.opacity.scale.90 class="bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">

                <!-- Icon -->
                <span class="text-2xl">✅</span>

                <!-- Message -->
                <div>
                    <h3 class="font-bold">Success!</h3>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>

                <!-- Close Button -->
                <button @click="showSuccess = false" class="ml-auto text-white text-lg font-bold">
                    &times;
                </button>
            </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
            <div x-show="showError" x-transition.opacity.scale.90 class="bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">

                <!-- Icon -->
                <span class="text-2xl">⚠️</span>

                <!-- Message -->
                <div>
                    <h3 class="font-bold">Error!</h3>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>

                <!-- Close Button -->
                <button @click="showError = false" class="ml-auto text-white text-lg font-bold">
                    &times;
                </button>
            </div>
            @endif

        </div>
        @endif

        <!-- Main Content -->
        <div id="admincontent" class="mt-14 content-wrapper ml-64 p-4 bg-white duration-300">
            {{-- <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Laporan Rekap Penjualan Perusahaan</h1> --}}
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Sales Recap by Company</h1>
            @if(empty($aiInsight))
            <div class="my-6 text-center">
                <a href="{{ request()->fullUrlWithQuery(['generate_ai' => 'true']) }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                    Buat Analisis AI
                </a>
            </div>
            @endif

            {{-- Bagian ini tidak berubah. Ia hanya akan tampil jika $aiInsight tidak kosong. --}}
            @if(!empty($aiInsight))
            <div class="ai-insight mt-4 p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Analisis Penjualan</h3>
                <div class="prose max-w-none">
                    {!! \Illuminate\Support\Str::markdown($aiInsight) !!}
                </div>
            </div>
            @endif

            <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4">
                <!-- Search -->
                <form method="GET" action="{{ route('rekappenjualanperusahaan.index') }}" class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                        <input type="month" name="search" placeholder="Search by MM / YYYY" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                    </div>

                    <button type="submit" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" aria-label="Search">
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
                <button class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2" data-modal-target="#addEventModal">
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

                    <!-- Event Table -->
                    <div class="overflow-x-auto bg-white shadow-md">
                        <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Date</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Company</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Value</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekappenjualanperusahaans as $rekappenjualanperusahaan)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->tanggal_formatted }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->perusahaan->nama_perusahaan }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->total_penjualan_formatted }}</td>
                                    <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button class="text-red-600 bg-transparent px-3 py-2 rounded" data-modal-target="#editEventModal{{ $rekappenjualanperusahaan->id }}">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('rekappenjualanperusahaan.destroy', $rekappenjualanperusahaan->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 bg-transparent px-3 py-2 rounded" onclick="return confirm('Are you sure to delete?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal for Edit Event -->
                                <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $rekappenjualanperusahaan->id }}">
                                    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                        <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                        <form method="POST" action="{{ route('rekappenjualanperusahaan.update', $rekappenjualanperusahaan->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="tanggal" class="block text-sm font-medium">Date</label>
                                                    <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $rekappenjualanperusahaan->tanggal }}" required>
                                                </div>
                                                <div>
                                                    <label for="perusahaan_id" class="block text-sm font-medium">Choose Company</label>
                                                    <select name="perusahaan_id" class="w-full p-2 border rounded" required>
                                                        @foreach ($perusahaans as $perusahaan)
                                                        <option value="{{ $perusahaan->id }}" {{ $rekappenjualanperusahaan->perusahaan_id == $perusahaan->id ? 'selected' : '' }}>
                                                            {{ $perusahaan->nama_perusahaan }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="total_penjualan" class="block text-sm font-medium">Total Sales</label>
                                                    <input type="number" name="total_penjualan" class="w-full p-2 border rounded" value="{{ $rekappenjualanperusahaan->total_penjualan }}" required>
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
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-center items-center mt-2 mb-4 p-4 bg-gray-50 rounded-lg">
                        <!-- Dropdown untuk memilih jumlah data per halaman -->
                        <div class="flex items-center">
                            <label for="perPage" class="mr-2 text-sm text-gray-600">Show</label>
                            <select id="perPage" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" onchange="changePerPage(this.value)">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                            </select>
                            <span class="ml-2 text-sm text-gray-600">data per page</span>
                        </div>
                    </div>

                    <div class="m-4">
                        {{ $rekappenjualanperusahaans->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
            <div id="formChart" class="visible">
                <div class="flex flex-col mx-auto bg-white p-6 mt-4 rounded-lg shadow-xl border border-grey-500">
                    <h1 class="text-2xl font-bold text-red-600 mb-2 mx-auto font-montserrat text-start">Sales Recap by Company Chart</h1>

                    <div class="mt-6 self-center w-full h-96 flex justify-center">
                        <div class="w-full h-96 overflow-y-auto overflow-x-hidden" style="max-height: 24rem;">
                            <canvas id="chart" style="min-width:600px; min-height:400px;"></canvas>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button onclick="exportToPDF()" class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-red-600 transition duration-300 ease-in-out">
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

            <!-- Modal untuk Add Event -->
            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
                <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                    <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
                    <form method="POST" action="{{ route('rekappenjualanperusahaan.store') }}" enctype="multipart/form-data">
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
                                <label for="total_penjualan" class="block text-sm font-medium">Total Sales</label>
                                <input type="number" name="total_penjualan" class="w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
                        </div>
                    </form>
                </div>
            </div>

</body>

<script>
        //toogle form
        const toggleFormButton = document.getElementById('toggleFormButton');
        const formContainer = document.getElementById('formContainer');

        toggleFormButton.addEventListener('click', () => {
            formContainer.classList.toggle('hidden');
        });

    const toggleChartButton = document.getElementById('toggleChartButton');
        const formChart = document.getElementById('formChart');

        toggleChartButton.addEventListener('click', () => {
            formChart.classList.toggle('hidden');
        });


    const chartCanvas = document.getElementById('chart');
    // Mengatur tombol untuk membuka modal add
    document.querySelector('[data-modal-target="#addEventModal"]').addEventListener('click', function() {
        const modal = document.querySelector('#addEventModal');
        modal.classList.remove('hidden');
    });
        
    // Mengatur tombol untuk membuka modal edit
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            // Menemukan modal berdasarkan ID yang diberikan di data-modal-target
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.classList.remove('hidden'); // Menampilkan modal
            }
        });
    });
    // Menutup modal ketika tombol Close ditekan
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.fixed');
            modal.classList.add('hidden'); // Menyembunyikan modal
        });
    });

    var chartData = @json($chartData);

    var ctx = document.getElementById('chart').getContext('2d');

    var horizontalBarChart = new Chart(ctx, {
        // Tipe chart tetap 'bar'
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets,
        },
        options: {
            // --- PERUBAHAN UTAMA ---
            // Mengubah sumbu utama menjadi sumbu Y
            indexAxis: 'y',
            // ---------------------

            responsive: true,
            maintainAspectRatio: false, // Memungkinkan chart menyesuaikan tinggi
            plugins: {
                legend: {
                    display: false, // Sembunyikan legenda
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let value = tooltipItem.raw;
                            // Menggunakan tooltipItem.dataset.label yang lebih standar
                            return tooltipItem.dataset.label + ': ' + 'Rp ' + value.toLocaleString('id-ID');
                        },
                    },
                },
            },
            scales: {
                // Konfigurasi sumbu X (sekarang menjadi sumbu NILAI)
                x: {
                    beginAtZero: true,
                    title: {
                        display: false,
                    },
                    ticks: {
                        // Format angka pada sumbu nilai
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + ' Jt';
                            }
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                    },
                },
                // Konfigurasi sumbu Y (sekarang menjadi sumbu KATEGORI)
                y: {
                    title: {
                        display: false,
                    },
                },
            },
            layout: {
                padding: {
                    // Beri ruang di kanan untuk label nilai
                    right: 80,
                    left: 20
                },
            },
        },
        plugins: [{
            // Plugin custom untuk menampilkan nilai di ujung setiap bar
            id: 'custom_data_labels_horizontal',
            afterDatasetsDraw: function(chart) {
                const { ctx, data, chartArea: { top, bottom, left, right }, scales: { x, y } } = chart;
                
                ctx.save();
                ctx.font = 'bold 14px sans-serif';
                ctx.fillStyle = 'black';
                ctx.textBaseline = 'middle';

                data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    // Pastikan dataset adalah bar dan terlihat
                    if (meta.type !== 'bar' || !meta.visible) {
                        return;
                    }

                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        
                        // Posisi teks di sebelah kanan bar
                        const textPositionX = bar.x + 8;
                        
                        // Atur perataan teks
                        ctx.textAlign = 'left';
                        
                        // Tampilkan teks
                        ctx.fillText('Rp ' + value.toLocaleString('id-ID'), textPositionX, bar.y);
                    });
                });
                ctx.restore();
            }
        }]
    });
    
// JavaScript Function
async function exportToPDF() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Pastikan meta tag CSRF disertakan.');
        return;
    }

    // Ambil data dari tabel
    const items = Array.from(document.querySelectorAll('#data-table tr')).map(row => {
        const cells = row.querySelectorAll('td');
        return {
            tanggal_formatted: cells[0]?.innerText.trim() || '',
            perusahaan: cells[1]?.innerText.trim() || '',
            total_penjualan_formatted: cells[2]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal_formatted && item.perusahaan && item.total_penjualan_formatted)
        .map(item => `
            <tr>    
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal_formatted}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.perusahaan}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.total_penjualan_formatted}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    const chartCanvas = document.querySelector('#chart');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        return;
    }

    const chartBase64 = chartCanvas.toDataURL();

    try {
        const response = await fetch('/marketings/rekappenjualanperusahaan/export-pdf', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                table: pdfTable,
                chart: chartBase64,
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Laporan_rekap_penjualan_perusahaan.pdf';
        document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            alert('Gagal mengekspor PDF.');
        }
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('Terjadi kesalahan saat mengekspor PDF.');
    }
}

function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    window.location.href = url.toString();
}

function changePerPage(value) {
    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);
    
    searchParams.set('per_page', value);
    if (!searchParams.has('page')) {
        searchParams.set('page', 1);
    }
    
    window.location.href = url.pathname + '?' + searchParams.toString();
}

async function exportToPDF1() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Pastikan meta tag CSRF disertakan.');
        return;
    }

    // Ambil data dari tabel
    const items = Array.from(document.querySelectorAll('#data-table tr')).map(row => {
        const cells = row.querySelectorAll('td');
        return {
            tanggal: cells[0]?.innerText.trim() || '',
            perusahaan: cells[1]?.innerText.trim() || '',
            total_penjualan_formatted: cells[2]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal && item.perusahaan && item.total_penjualan_formatted)
        .map(item => `
            <tr>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.perusahaan}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.total_penjualan_formatted}</td>
            </tr>
        `).join('');

    const chartCanvas = document.querySelector('#chart');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        return;
    }

    const chartBase64 = chartCanvas.toDataURL();

    try {
        const response = await fetch('/exportall', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                table2: tableContent,
                chart2: chartBase64,
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Laporan_rekap_penjualan_perusahaan.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            const errorData = await response.json();
            alert(errorData.message || 'Gagal mengekspor PDF.');
        }
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('Terjadi kesalahan saat mengekspor PDF.');
    }
}

// Add event listener to trigger export
document.addEventListener('DOMContentLoaded', function() {
    // Custom event listener for export
    document.addEventListener('triggerPDFExport', function(e) {
        exportToPDF1();
    });
});

</script>
</html>