<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- The CSRF token will be dynamically injected by Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Package Status Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Assuming these are Laravel Vite directives -->
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
            <div x-show="showError" x-transition.opacity.scale.90 class="bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">
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
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Package Status Report</h1>
            
            @if(empty($aiInsight))
            <div class="my-6 text-center">
                <a href="{{ request()->fullUrlWithQuery(['generate_ai' => 'true']) }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                    Buat Analisis AI
                </a>
            </div>
            @endif

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
                <form method="GET" action="{{ route('statuspaket.index') }}" class="flex items-center gap-2">
                    <!-- Start Date with Tooltip -->
                    <div class="relative group">
                        <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                        </div>
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                            <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Start Date</span>
                            <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                        </div>
                    </div>

                    <span>To</span>

                    <!-- End Date with Tooltip -->
                    <div class="relative group">
                        <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                        </div>
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                            <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">End Date</span>
                            <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                        </div>
                    </div>

                    <!-- Search Button with Tooltip -->
                    <div class="relative group">
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
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                            <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Search Data</span>
                            <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                        </div>
                    </div>
                </form>

                <!-- Add Data Button with Tooltip -->
                <div class="relative group">
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
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                        <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Add New Data</span>
                        <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                    </div>
                </div>

                <!-- Toggle Table Button with Tooltip -->
                <div class="relative group">
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
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                        <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Show/Hide Table</span>
                        <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                    </div>
                </div>

                <!-- Toggle Chart Button with Tooltip -->
                <div class="relative group">
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
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                        <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Show/Hide Chart</span>
                        <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                    </div>
                </div>
            </div>

            <div id="formContainer" class="hidden">
                <div class="mx-auto bg-white p-6 rounded-lg shadow">
                    <div class="overflow-x-auto bg-white shadow-md">
                        <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Nilai Paket</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statuspakets as $statuspaket)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $statuspaket->tanggal_formatted }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $statuspaket->status }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $statuspaket->total_paket }}</td>
                                    <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                        <button class="text-red-600 bg-transparent px-3 py-2 rounded" data-modal-target="#editEventModal{{ $statuspaket->id_statuspaket }}">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <form method="POST" action="{{ route('statuspaket.destroy', $statuspaket->id_statuspaket) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 bg-transparent px-3 py-2 rounded" onclick="confirmDelete(this)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-center items-center mt-2 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <label for="perPage" class="mr-2 text-sm text-gray-600">Tampilkan</label>
                            <select id="perPage" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                            </select>
                            <span class="ml-2 text-sm text-gray-600">data per halaman</span>
                        </div>
                    </div>
                    <div class="m-4">
                        {{ $statuspakets->withQueryString()->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>

            <div id="formChart" class="visible">
                <div class="flex flex-col mx-auto bg-white p-6 mt-4 rounded-lg shadow-xl border border-grey-500">
                    <h1 class="text-2xl font-bold text-red-600 mb-2 mx-auto font-montserrat text-start">Package Status Report Chart</h1>
                    <div class="mt-6 self-center w-full flex justify-center" style="height: 450px;">
                        <canvas id="chart"></canvas>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <div class="relative group">
                            <button id="exportPdfButton" class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out">
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
                            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                                <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Export To PDF</span>
                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            @foreach ($statuspakets as $statuspaket)
            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $statuspaket->id_statuspaket }}">
                <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                    <h3 class="text-xl font-semibold mb-4">Ubah Data</h3>
                    <form method="POST" action="{{ route('statuspaket.update', $statuspaket->id_statuspaket) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                                <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $statuspaket->tanggal }}" required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium">Status</label>
                                <select name="status" class="w-full p-2 border rounded" required>
                                    <option value="Surat Pesanan" {{ $statuspaket->status == 'Surat Pesanan' ? 'selected' : '' }}>Surat Pesanan</option>
                                    <option value="Surat Pertanggungjawaban" {{ $statuspaket->status == 'Surat Pertanggungjawaban' ? 'selected' : '' }}>Surat Pertanggungjawaban</option>
                                    <option value="Keuangan" {{ $statuspaket->status == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="Dokumen Akhir" {{ $statuspaket->status == 'Dokumen Akhir' ? 'selected' : '' }}>Dokumen Akhir</option>
                                    <option value="Finish" {{ $statuspaket->status == 'Finish' ? 'selected' : '' }}>Finish</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_paket" class="block text-sm font-medium">Nilai Paket</label>
                                <input type="number" name="total_paket" class="w-full p-2 border rounded" value="{{ $statuspaket->total_paket }}" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Tutup</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
                <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                    <h3 class="text-xl font-semibold mb-4">Tambah Data Baru</h3>
                    <form method="POST" action="{{ route('statuspaket.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                                <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium">Status</label>
                                <select name="status" class="w-full p-2 border rounded" required>
                                    <option value="Surat Pesanan">Surat Pesanan</option>
                                    <option value="Surat Pertanggungjawaban">Surat Pertanggungjawaban</option>
                                    <option value="Keuangan">Keuangan</option>
                                    <option value="Dokumen Akhir">Dokumen Akhir</option>
                                    <option value="Finish">Finish</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_paket" class="block text-sm font-medium">Nilai Paket</label>
                                <input type="number" name="total_paket" class="w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Tutup</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
// --- Custom Delete Confirmation ---
// Replaces the native confirm() which can be blocked or look inconsistent.
function confirmDelete(button) {
    const form = button.closest('form');
    // You can replace this with a more sophisticated custom modal if desired
    const userConfirmed = window.confirm('Apakah Anda yakin ingin menghapus?');
    if (userConfirmed) {
        form.submit();
    }
}

// --- Main script execution after DOM is loaded ---
document.addEventListener('DOMContentLoaded', function() {

    // --- Toggle Visibility for Table and Chart ---
    const toggleFormButton = document.getElementById('toggleFormButton');
    const formContainer = document.getElementById('formContainer');
    if (toggleFormButton && formContainer) {
        toggleFormButton.addEventListener('click', () => {
            formContainer.classList.toggle('hidden');
        });
    }

    const toggleChartButton = document.getElementById('toggleChartButton');
    const formChart = document.getElementById('formChart');
    if (toggleChartButton && formChart) {
        toggleChartButton.addEventListener('click', () => {
            formChart.classList.toggle('hidden');
        });
    }

    // --- Modal Handling ---
    // Open modal
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        });
    });

    // Close modal
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.fixed.z-50');
            if (modal) {
                modal.classList.add('hidden');
            }
        });
    });
    
    // --- Pagination 'Per Page' Selector ---
    const perPageSelect = document.getElementById('perPage');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', selectedValue);
            // Go to the first page when changing item count
            currentUrl.searchParams.set('page', '1'); 
            window.location.href = currentUrl.toString();
        });
    }

    // --- Chart.js Initialization ---
    const chartData = @json($chartData);
    const ctx = document.getElementById('chart')?.getContext('2d');

    if (ctx && chartData) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets,
            },
            options: {
                indexAxis: 'y', // This makes the bar chart horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw;
                                return `${tooltipItem.dataset.label}: ${value.toLocaleString('id-ID')} Packages`;
                            },
                        },
                    },
                    // Custom plugin to display values at the end of each bar
                    datalabels: {
                        anchor: 'end',
                        align: 'right',
                        formatter: (value, context) => {
                             return value.toLocaleString('id-ID') + ' Packages';
                        },
                        color: 'black',
                        font: {
                            weight: 'bold',
                            size: 14,
                        },
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: false },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toLocaleString('id-ID') + 'M';
                                }
                                if (value >= 1000) {
                                    return (value / 1000).toLocaleString('id-ID') + 'K';
                                }
                                return value.toLocaleString('id-ID');
                            },
                        },
                    },
                    y: {
                        title: { display: false },
                    },
                },
            },
             plugins: [{
                id: 'custom_data_labels_horizontal',
                afterDatasetsDraw: function(chart) {
                    const { ctx, data } = chart;
                    ctx.save();
                    ctx.font = 'bold 14px sans-serif';
                    ctx.fillStyle = 'black';
                    ctx.textBaseline = 'middle';

                    data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (meta.type !== 'bar' || !meta.visible) return;

                        meta.data.forEach((bar, index) => {
                            const value = dataset.data[index];
                            const textPositionX = bar.x + 8;
                            ctx.textAlign = 'left';
                            ctx.fillText(value.toLocaleString('id-ID') + " Packages", textPositionX, bar.y);
                        });
                    });
                    ctx.restore();
                }
            }]
        });
    }

    // --- PDF Export Functionality ---
    const exportButton = document.getElementById('exportPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', exportToPDF);
    }

    async function exportToPDF() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token not found.');
            // You could show a user-friendly error message here
            return;
        }

        // Extract table data
        const items = Array.from(document.querySelectorAll('#data-table tbody tr')).map(row => {
            const cells = row.querySelectorAll('td');
            return {
                tanggal: cells[0]?.innerText.trim() || '',
                status: cells[1]?.innerText.trim() || '',
                total_paket: cells[2]?.innerText.trim() || '',
            };
        });

        const tableContent = items.map(item => `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${item.tanggal}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${item.status}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${item.total_paket}</td>
            </tr>
        `).join('');

        // Get chart as image
        const chartCanvas = document.querySelector('#chart');
        if (!chartCanvas) {
            console.error('Chart canvas element not found.');
            return;
        }
        const chartBase64 = chartCanvas.toDataURL('image/png');

        // Send data to the server to generate PDF
        try {
            const response = await fetch('/marketings/statuspaket/export-pdf', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/pdf',
                },
                body: JSON.stringify({
                    table: tableContent,
                    chart: chartBase64,
                }),
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'laporan_status_paket.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
            } else {
                console.error('Failed to export PDF:', response.status, await response.text());
                // You could show a user-friendly error message here
            }
        } catch (error) {
            console.error('An error occurred while exporting PDF:', error);
        }
    }
});
</script>
</body>
</html>
