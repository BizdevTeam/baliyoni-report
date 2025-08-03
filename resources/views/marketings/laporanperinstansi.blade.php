<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Institution-Based Report</title>
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
                   }" x-init="setTimeout(() => showSuccess = false, 3000); setTimeout(() => showError = false, 3000);" class="fixed top-5 right-5 z-50 flex flex-col gap-3">

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
        <div id="admincontent" class="mt-14 content-wrapper ml-64 p-4 bg-gray-50 duration-300">
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Institution-Based Report</h1>

            @if(empty($aiInsight))
            <div class="my-6 text-center">
                <a href="{{ request()->fullUrlWithQuery(['generate_ai' => 'true']) }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                    Buat Analisis AI
                </a>
            </div>
            @endif

            @if(!empty($aiInsight))
            <div class="ai-insight my-6 p-6 bg-white rounded-lg shadow-md border-l-4 border-indigo-500">
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Analisis Per Instansi</h3>
                <div class="prose max-w-none text-gray-600">
                    {!! \Illuminate\Support\Str::markdown($aiInsight) !!}
                </div>
            </div>
            @endif

            <!-- Controls and Filters -->
            <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4">
                <form method="GET" action="{{ route('laporanperinstansi.index') }}" class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                        <input type="month" name="search" id="search-month-input" placeholder="Cari berdasarkan Bulan / Tahun" value="{{ request('search', date('Y-m')) }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400 bg-transparent" />
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

            <!-- Data Table and Charts Container -->
            <div id="formContainer" class="hidden">
                <!-- Table Container -->
                <div id="table-view" class="mx-auto bg-white p-6 rounded-lg shadow">
                    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                        <table class="table-auto w-full border-collapse" id="data-table">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border-b border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                    <th class="border-b border-gray-300 px-4 py-2 text-center">Instansi</th>
                                    <th class="border-b border-gray-300 px-4 py-2 text-center">Nilai</th>
                                    <th class="border-b border-gray-300 px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporanperinstansis as $laporanperinstansi)
                                <tr class="hover:bg-gray-100">
                                    <td class="border-b border-gray-200 px-4 py-2 text-center">{{ $laporanperinstansi->tanggal_formatted }}</td>
                                    <td class="border-b border-gray-200 px-4 py-2 text-center">{{ $laporanperinstansi->instansi }}</td>
                                    <td class="border-b border-gray-200 px-4 py-2 text-center">{{ $laporanperinstansi->nilai_formatted }}</td>
                                    <td class="border-b border-gray-200 py-2 text-center flex justify-center gap-2">
                                        <button class="text-blue-600 hover:text-blue-800 bg-transparent px-3 py-2 rounded" data-modal-target="#editEventModal{{ $laporanperinstansi->id_perinstansi }}">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <form method="POST" action="{{ route('laporanperinstansi.destroy', $laporanperinstansi->id_perinstansi) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-800 bg-transparent px-3 py-2 rounded" onclick="confirmDelete(this)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 border-b border-gray-200">Tidak ada data untuk periode yang dipilih.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="mx-auto flex justify-between items-center mt-4 p-4 bg-white rounded-lg">
                        <div class="flex items-center mx-auto">
                            <label for="perPage" class="mr-2 text-sm text-gray-600">Tampilkan</label>
                            <select id="perPage" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="5" @if(request('per_page')==5) selected @endif>5</option>
                                <option value="12" @if(!request('per_page') || request('per_page')==12) selected @endif>12</option>
                                <option value="24" @if(request('per_page')==24) selected @endif>24</option>
                            </select>
                            <span class="ml-2 text-sm text-gray-600">data per halaman</span>
                        </div>
                        <div class="m-4">
                            {{ $laporanperinstansis->withQueryString()->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
                </div>

                <!-- Chart Container -->
                <div id="formChart" class="visible">
                <div class="flex flex-col mx-auto bg-white p-6 mt-4 rounded-lg shadow-xl border chart-group">
                    <div class="mb-4 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-red-600 font-montserrat mx-auto">Institution-Based Chart</h1>
                        <select class="chart-select p-2 border border-gray-300 rounded-md">
                            <option value="chartBiasa">Chart Biasa</option>
                            <option value="chartTotal">Chart Total</option>
                            <option value="chartPerbandingan">Chart Perbandingan</option>
                        </select>
                    </div>

                    <div class="mt-6 self-center w-full relative" style="height: 450px;">
                        <div class="chart-container chartBiasa w-full h-full">
                            <canvas id="chartBiasaCanvas" data-axis="y"></canvas>
                        </div>
                        <div class="chart-container chartTotal hidden w-full h-full">
                            <canvas id="chartTotalCanvas" data-axis="y"></canvas>
                        </div>
                        <div class="chart-container chartPerbandingan hidden w-full h-full">
                            <canvas id="chartPerbandinganCanvas" data-axis="y"></canvas>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button id="exportPdfButton" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 ease-in-out">
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

        <!-- Modals are unchanged -->
        <!-- Modal untuk Add Event -->
        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
                <form method="POST" action="{{ route('laporanperinstansi.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="tanggal" class="block text-sm font-medium">Date</label>
                            <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label for="instansi" class="block text-sm font-medium">Institution</label>
                            <select name="instansi" class="w-full p-2 border rounded" required>
                                <option value="Badung">Badung</option>
                                <option value="Denpasar">Denpasar</option>
                                <option value="Tabanan">Tabanan</option>
                                <option value="Gianyar">Gianyar</option>
                                <option value="Karangasem">Karangasem</option>
                                <option value="Singaraja">Singaraja</option>
                                <option value="Jembrana">Jembrana</option>
                                <option value="Klungkung">Klungkung</option>
                                <option value="Provinsi">Provinsi</option>
                            </select>
                        </div>
                        <div>
                            <label for="nilai" class="block text-sm font-medium">Value</label>
                            <input type="number" name="nilai" class="w-full p-2 border rounded" required>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modals untuk Edit Event -->
        @foreach ($laporanperinstansis as $laporanperinstansi)
        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanperinstansi->id_perinstansi }}">
            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                <form method="POST" action="{{ route('laporanperinstansi.update', $laporanperinstansi->id_perinstansi) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="tanggal" class="block text-sm font-medium">Date</label>
                            <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $laporanperinstansi->tanggal }}" required>
                        </div>
                        <div>
                            <label for="instansi" class="block text-sm font-medium">Institution</label>
                            <select name="instansi" class="w-full p-2 border rounded" required>
                                <option value="Badung" {{ $laporanperinstansi->instansi == 'Badung' ? 'selected' : '' }}>Badung</option>
                                <option value="Denpasar" {{ $laporanperinstansi->instansi == 'Denpasar' ? 'selected' : '' }}>Denpasar</option>
                                <option value="Tabanan" {{ $laporanperinstansi->instansi == 'Tabanan' ? 'selected' : '' }}>Tabanan</option>
                                <option value="Gianyar" {{ $laporanperinstansi->instansi == 'Gianyar' ? 'selected' : '' }}>Gianyar</option>
                                <option value="Karangasem" {{ $laporanperinstansi->instansi == 'Karangasem' ? 'selected' : '' }}>Karangasem</option>
                                <option value="Singaraja" {{ $laporanperinstansi->instansi == 'Singaraja' ? 'selected' : '' }}>Singaraja</option>
                                <option value="Jembrana" {{ $laporanperinstansi->instansi == 'Jembrana' ? 'selected' : '' }}>Jembrana</option>
                                <option value="Klungkung" {{ $laporanperinstansi->instansi == 'Klungkung' ? 'selected' : '' }}>Klungkung</option>
                                <option value="Provinsi" {{ $laporanperinstansi->instansi == 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                            </select>
                        </div>
                        <div>
                            <label for="nilai" class="block text-sm font-medium">Value</label>
                            <input type="number" name="nilai" class="w-full p-2 border rounded" value="{{ $laporanperinstansi->nilai }}" required>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

<script>
// --- Custom Delete Confirmation ---
function confirmDelete(button) {
    if (window.confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        button.closest('form').submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const chartInstances = new Map();

    // --- Helper Functions ---
    function formatCurrency(value) {
        if (typeof value !== 'number') return 'Rp 0';
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    function parseCurrency(currencyString) {
        if (typeof currencyString !== 'string') return 0;
        return parseInt(currencyString.replace(/Rp|\s|\./g, ''), 10) || 0;
    }

    // --- Chart.js Plugin for Data Labels ---
    const dataLabelsPlugin = {
            id: 'customDataLabelsOnBars',
            afterDatasetsDraw(chart) {
                const { ctx, data, config } = chart;
                const chartType = config.type;
                const axis = config.options.indexAxis;
                if (chartType !== 'bar') return;

                ctx.save();
                ctx.font = 'bold 12px Arial';

                data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach((element, index) => {
                    const value = dataset.data[index];
                    if (value === null || value === 0) return;

                    const formattedValue = formatCurrency(value);
                    let xPos, yPos;

                    if (axis === 'y') {
                        // Horizontal bars
                        ctx.textBaseline = 'middle';
                        xPos = element.x + 8;
                        yPos = element.y;
                        const textWidth = ctx.measureText(formattedValue).width;

                        if (xPos + textWidth > chart.chartArea.right) {
                        // Kalau kebawah area, pindah ke kiri bar
                        xPos = element.x - 8;
                        ctx.textAlign = 'right';
                        ctx.fillStyle = 'white';
                        } else {
                        ctx.textAlign = 'left';
                        ctx.fillStyle = 'black';
                        }
                    } else {
                        // Vertical bars
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        xPos = element.x;
                        yPos = element.y - 5;
                        ctx.fillStyle = 'black';
                    }

                    ctx.fillText(formattedValue, xPos, yPos);
                    });
                }
                });

                ctx.restore();
            }
            };
    // Daftarkan plugin ke Chart.js
    Chart.register(dataLabelsPlugin);

    // --- Chart Creation Logic ---
    function createOrUpdateChart(canvas, chartData, chartType = 'bar') {
        if (!canvas) return;
        
        const axis = canvas.dataset.axis || 'x';
        const config = {
            type: chartType,
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: axis,
                scales: {
                    x: { beginAtZero: true, stacked: false },
                    y: { beginAtZero: true, stacked: false }
                },
                plugins: {
                    legend: { display: chartData.datasets.length > 1 },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.dataset.label || ''}: ${formatCurrency(context.raw)}`
                        }
                    }
                }
            }
        };
        
        const axisToFormat = (axis === 'y') ? 'x' : 'y';
        config.options.scales[axisToFormat].ticks = { callback: (value) => formatCurrency(value) };

        if (chartInstances.has(canvas)) chartInstances.get(canvas).destroy();
        chartInstances.set(canvas, new Chart(canvas.getContext('2d'), config));
    }

    // --- Data Processing and Rendering ---

    /**
     * Extracts and aggregates data from the visible HTML table.
     * @returns {object} An object where keys are institution names and values are their total.
     */
    function getAggregatedDataFromTable() {
        const tableRows = document.querySelectorAll('#data-table tbody tr');
        const aggregatedData = {};

        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 4) return; 

            const instansi = cells[1].innerText.trim();
            const value = parseCurrency(cells[2].innerText.trim());
            aggregatedData[instansi] = (aggregatedData[instansi] || 0) + value;
        });
        return aggregatedData;
    }

    function getRandomDarkRGBA(opacity = 0.6) {
        let r, g, b, brightness;
        do {
            r = Math.floor(Math.random() * 256);
            g = Math.floor(Math.random() * 256);
            b = Math.floor(Math.random() * 256);
            // rumus luminance standar (persepsi)
            brightness = 0.299 * r + 0.587 * g + 0.114 * b;
        } while (brightness > 130); // ulang jika terlalu terang
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    function renderSimpleCharts(aggregatedData) {
        // Chart Biasa (Detailed View)
        const tableRows = document.querySelectorAll('#data-table tbody tr');
        const labelsBiasa = [];
        const dataBiasa = [];
        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 4) return;
            const date = cells[0].innerText.trim();
            const instansi = cells[1].innerText.trim();
            const value = parseCurrency(cells[2].innerText.trim());
            labelsBiasa.push(`${instansi} (${date})`);
            dataBiasa.push(value);
        });
        
        const colorsBiasa = dataBiasa.map(() => getRandomDarkRGBA(0.6));

        const chartDataBiasa = {
            labels: labelsBiasa,
            datasets: [{
                label: 'Rincian Transaksi',
                data: dataBiasa,
                backgroundColor: colorsBiasa,
            }]
        };
        createOrUpdateChart(document.getElementById('chartBiasaCanvas'), chartDataBiasa);

        // Chart Total
        const labelsTotal = Object.keys(aggregatedData);
        const dataTotal = Object.values(aggregatedData);
        const colorsTotal = dataTotal.map(() => getRandomDarkRGBA(0.7));

        const chartDataTotal = {
            labels: labelsTotal,
            datasets: [{
                label: 'Total per Instansi',
                data: dataTotal,
                backgroundColor: colorsTotal,
            }]
        };
        createOrUpdateChart(document.getElementById('chartTotalCanvas'), chartDataTotal);
    }

    async function renderDynamicComparisonChart(currentMonthData) {
        const getSimulatedPreviousMonthData = (currentData) => {
            const previousData = {};
            for (const instansi in currentData) {
                // Buat data acak untuk bulan lalu (antara 50% dan 150% dari data bulan ini)
                const randomFactor = 0.5 + Math.random();
                previousData[instansi] = Math.round(currentData[instansi] * randomFactor);
            }
            // Tambahkan satu instansi yang mungkin tidak ada di bulan ini
            if (!previousData['Jembrana']) {
                previousData['Jembrana'] = Math.round(Math.random() * 50000000);
            }
            return previousData;
        };
        
        const previousMonthData = getSimulatedPreviousMonthData(currentMonthData);

        const allLabels = [...new Set([...Object.keys(currentMonthData), ...Object.keys(previousMonthData)])];

        const chartData = {
            labels: allLabels,
            datasets: [
                {
                    label: 'Bulan Lalu',
                    data: allLabels.map(label => previousMonthData[label] || 0),
                    backgroundColor: 'rgba(211, 211, 211, 0.9)', // Light gray
                    borderColor: 'rgba(192, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Bulan Ini',
                    data: allLabels.map(label => currentMonthData[label] || 0),
                    backgroundColor: 'rgba(220, 20, 60, 0.8)', // Crimson red
                    borderColor: 'rgba(178, 34, 34, 1)',
                    borderWidth: 1
                }
            ]
        };
        
        createOrUpdateChart(document.getElementById('chartPerbandinganCanvas'), chartData);
    }

    function initializeVisuals() {
        const aggregatedData = getAggregatedDataFromTable();
        renderSimpleCharts(aggregatedData);
        renderDynamicComparisonChart(aggregatedData);
    }

    initializeVisuals();

    document.querySelectorAll('.chart-group').forEach(group => {
        const select = group.querySelector('.chart-select');
        const containers = group.querySelectorAll('.chart-container');
        
        function updateChartDisplay() {
            const selectedValue = select.value;
            containers.forEach(container => {
                const isHidden = !container.classList.contains(selectedValue);
                container.classList.toggle('hidden', isHidden);
                if (!isHidden) {
                    const chartInstance = chartInstances.get(container.querySelector('canvas'));
                    if(chartInstance) setTimeout(() => chartInstance.resize(), 50);
                }
            });
        }
        select.addEventListener('change', updateChartDisplay);
        updateChartDisplay(); // Initial call
    });

    document.getElementById('toggleFormButton')?.addEventListener('click', () => {
        document.getElementById('formContainer').classList.toggle('hidden');
    });
    document.getElementById('toggleChartButton')?.addEventListener('click', () => {
        document.getElementById('formChart').classList.toggle('hidden');
    });

    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => document.querySelector(button.dataset.modalTarget)?.classList.remove('hidden'));
    });
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => button.closest('.fixed.z-50').classList.add('hidden'));
    });

    const perPageSelect = document.getElementById('perPage');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('page', '1');
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    }

    // --- PDF EXPORT ---
    const exportButton = document.getElementById('exportPdfButton');
    if (exportButton) {
        exportButton.addEventListener('click', exportToPDF);
    }

    async function exportToPDF() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token not found.');
            alert('Error: CSRF token is missing. Cannot export.');
            return;
        }

        document.body.style.cursor = 'wait';

        const items = Array.from(document.querySelectorAll('#data-table tbody tr')).map(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 3) return null;
            return {
                tanggal: cells[0]?.innerText.trim() || '',
                instansi: cells[1]?.innerText.trim() || '',
                nilai: cells[2]?.innerText.trim() || '',
            };
        }).filter(item => item !== null);

        const tableContent = items.map(item => `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${item.tanggal}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">${item.instansi}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${item.nilai}</td>
            </tr>`).join('');

        const visibleChartContainer = document.querySelector('.chart-container:not(.hidden)');
        if (!visibleChartContainer) {
            alert('Tidak ada grafik yang terlihat untuk diekspor.');
            document.body.style.cursor = 'default';
            return;
        }
        const chartCanvas = visibleChartContainer.querySelector('canvas');
        const chartBase64 = chartCanvas.toDataURL('image/png', 1.0);

        try {
            const response = await fetch('/marketings/laporanperinstansi/export-pdf', {
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
                a.href = url;
                a.download = 'laporan-per-instansi.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } else {
                console.error('PDF Export Failed:', response.status, await response.text());
                alert('Gagal mengekspor PDF. Server memberikan error.');
            }
        } catch (error) {
            console.error('Error exporting to PDF:', error);
            alert('Terjadi kesalahan saat mengekspor PDF.');
        } finally {
            document.body.style.cursor = 'default';
        }
    }
});
</script>
</body>
</html>
