<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PT BOS Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
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
        }"
        x-init="setTimeout(() => showSuccess = false, 3000); setTimeout(() => showError = false, 3000);"
        class="fixed top-5 right-5 z-50 flex flex-col gap-3">

        <!-- Success Alert -->
        @if (session('success'))
        <div x-show="showSuccess" x-transition.opacity.scale.90
            class="bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">
            
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
        <div x-show="showError" x-transition.opacity.scale.90
            class="bg-red-600 text-white p-4 rounded-lg shadow-lg flex items-center gap-3 w-[500px]">
            
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
        <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">PT BOS Report</h1>
            @if(empty($aiInsight))
                <div class="my-6 text-center">
                    <a href="{{ request()->fullUrlWithQuery(['generate_ai' => 'true']) }}"
                    class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
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
        <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4 p-4">
            <!-- Search -->
            <form method="GET" action="{{ route('laporanptbos.index') }}" class="flex items-center gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input type="month" name="search" placeholder="Search by MM / YYYY" value="{{ request('search') }}"
                        class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                </div>

                <button type="submit"
                    class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2"
                    aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="40" stroke-dashoffset="40" d="M10.76 13.24c-2.34 -2.34 -2.34 -6.14 0 -8.49c2.34 -2.34 6.14 -2.34 8.49 0c2.34 2.34 2.34 6.14 0 8.49c-2.34 2.34 -6.14 2.34 -8.49 0Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="40;0"/></path><path stroke-dasharray="12" stroke-dashoffset="12" d="M10.5 13.5l-7.5 7.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s" values="12;0"/></path></g></svg>
                </button>
            </form>
            <button
                class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm mr-2"
                data-modal-target="#addEventModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M9 14h6"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="8;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M12 11v6"><animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="8;0"/></path></g></svg>
            </button>
        <button id="toggleFormButton"
                class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-4 py-2 rounded shadow-md hover:shadow-lg transition duration-300 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/></path><path stroke-dasharray="6" stroke-dashoffset="6" d="M12 14l-3 -3M12 14l3 -3"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.3s" values="6;0"/></path></g></svg>
        </button>
        </div>

        <div id="formContainer" class="visible">
            <div class="mx-auto bg-white p-6 rounded-lg shadow ">

                <div class="overflow-x-auto bg-white shadow-md">
                    <table class="table-fixed w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full" id="data-table">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center w-32">Date</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">Work</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">Last Month Condition</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">This Month Condition</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">Update</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">Implementation Plan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-64">Remarks</th>
                                <th class="border border-gray-300 px-4 py-2 text-center w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanptboss as $laporanptbos)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanptbos->tanggal_formatted }}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->pekerjaan !!}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->kondisi_bulanlalu !!}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->kondisi_bulanini !!}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->update !!}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->rencana_implementasi !!}</td>
                                    <td class="border border-gray-300 px-4 py-2 content-html align-center text-justify">{!! $laporanptbos->keterangan !!}</td>
                                    <td class="border border-gray-300 py-6 text-center ">
                                        <div class="flex justify-center items-center gap-3">
                                            <!-- Edit Button -->
                                            <button class="text-red-600 hover:text-red-800 transition" 
                                                data-modal-target="#editEventModal{{ $laporanptbos->id_ptbos }}">
                                                <i class="fa fa-pen text-lg"></i>
                                            </button>
                                            <!-- Delete Form -->
                                            <form method="POST" action="{{ route('laporanptbos.destroy', $laporanptbos->id_ptbos) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:text-red-800 transition" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="fa fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>                            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                        
                <!-- Modal for Edit Event -->
                @foreach ($laporanptboss as $laporanptbos)
                <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden " id="editEventModal{{ $laporanptbos->id_ptbos }}">
                    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                        <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                        <form method="POST" action="{{ route('laporanptbos.update', $laporanptbos->id_ptbos) }}" enctype="multipart/form-data" class="flex-grow overflow-y-auto space-y-4 pr-2">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                                <div>
                                    <label for="tanggal" class="block text-sm font-medium">Date</label>
                                    <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $laporanptbos->tanggal }}" required>
                                </div>
                                <div>
                                    <label for="pekerjaan" class="block text-sm font-medium">Work</label>
                                    <input type="hidden" name="pekerjaan" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-pekerjaan-input" value="{{ $laporanptbos->pekerjaan }}" required></input>
                                    <div id="edit-{{ $laporanptbos->id_ptbos }}-pekerjaan"></div>
                                </div>
                                <div>
                                    <label for="kondisi_bulanlalu" class="block text-sm font-medium">Last Month Condition</label>
                                    <input type="hidden" name="kondisi_bulanlalu" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-kondisi_bulanlalu-input" value="{{ $laporanptbos->kondisi_bulanlalu }}" required></input>
                                        <div id="edit-{{ $laporanptbos->id_ptbos }}-kondisi_bulanlalu"></div>
                                </div>
                                <div>
                                    <label for="kondisi_bulanini" class="block text-sm font-medium">This Month Condition</label>
                                    <input type="hidden" name="kondisi_bulanini" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-kondisi_bulanini-input" value="{{ $laporanptbos->kondisi_bulanini }}" required></input>
                                        <div id="edit-{{ $laporanptbos->id_ptbos }}-kondisi_bulanini"></div>
                                </div>
                                <div>
                                    <label for="update" class="block text-sm font-medium">Update</label>
                                    <input type="hidden" name="update" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-update-input" value="{{ $laporanptbos->update }}" required></input>
                                        <div id="edit-{{ $laporanptbos->id_ptbos }}-update"></div>
                                </div>
                                <div>
                                    <label for="rencana_implementasi" class="block text-sm font-medium">Implementasion Plan</label>
                                    <input type="hidden" name="rencana_implementasi" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-rencana_implementasi-input" value="{{ $laporanptbos->rencana_implementasi }}" required></input>
                                        <div id="edit-{{ $laporanptbos->id_ptbos }}-rencana_implementasi"></div>
                                </div>
                                <div>
                                    <label for="keterangan" class="block text-sm font-medium">Remarks</label>
                                    <input type="hidden" name="keterangan" class="w-full p-2 border rounded" id="edit-{{ $laporanptbos->id_ptbos }}-keterangan-input" value="{{ $laporanptbos->keterangan }}" required></input>
                                        <div id="edit-{{ $laporanptbos->id_ptbos }}-keterangan"></div>
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

        <!-- Pagination -->
        <div class="flex justify-center items-center mt-2 mb-4 p-4 bg-gray-50 rounded-lg">
            <!-- Dropdown untuk memilih jumlah data per halaman -->
            <div class="flex items-center">
                <label for="perPage" class="mr-2 text-sm text-gray-600">Tampilkan</label>
                <select 
                    id="perPage" 
                    class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    onchange="changePerPage(this.value)">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="12" {{ request('per_page') == 12 || !request('per_page') ? 'selected' : '' }}>12</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                </select>
                <span class="ml-2 text-sm text-gray-600">data per halaman</span>
            </div>
        </div>

        <div class="m-4">
            {{ $laporanptboss->links('pagination::tailwind') }}
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
    </div>

    <!-- Modal untuk Add Event -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 max-h-[90vh] p-6 rounded shadow-lg flex flex-col">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('laporanptbos.store') }}" enctype="multipart/form-data" id="addForm" class="flex-grow overflow-y-auto space-y-4 pr-2">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium">Date</label>
                    <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="pekerjaan" class="block text-sm font-medium">Work</label>
                    <input type="hidden" name="pekerjaan" id="pekerjaan-input" class="w-full p-2 border rounded">
                    <div id="editor-pekerjaan"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-pekerjaan">This field is required</div>
                </div>
                <div>
                    <label for="kondisi_bulanlalu" class="block text-sm font-medium">Last Month Condition</label>
                    <input type="hidden" name="kondisi_bulanlalu" id="kondisi_bulanlalu-input" class="w-full p-2 border rounded">
                    <div id="editor-kondisi_bulanlalu"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-kondisi_bulanlalu">This field is required</div>
                </div>
                <div>
                    <label for="kondisi_bulanini" class="block text-sm font-medium">This Month Condition</label>
                    <input type="hidden" name="kondisi_bulanini" id="kondisi_bulanini-input" class="w-full p-2 border rounded" rows="1"></input>
                    <div id="editor-kondisi_bulanini"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-kondisi_bulanini">This field is required</div>
                </div>
                <div>
                    <label for="update" class="block text-sm font-medium">Update</label>
                    <input type="hidden" name="update" id="update-input" class="w-full p-2 border rounded" required></input>
                    <div id="editor-update"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-update">This field is required</div>
                </div>
                <div>
                    <label for="rencana_implementasi" class="block text-sm font-medium">Implementasion Plan</label>
                    <input type="hidden" name="rencana_implementasi" id="rencana_implementasi-input" class="w-full p-2 border rounded">
                    <div id="editor-rencana_implementasi"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-rencana_implementasi">This field is required</div>
                </div>
                <div>
                    <label for="keterangan" class="block text-sm font-medium">Remarks</label>
                    <input type="hidden" name="keterangan" id="keterangan-input" class="w-full p-2 border rounded">
                    <div id="editor-keterangan"></div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="input-keterangan">This field is required</div>
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2 sticky bottom-0 bg-white py-4">
                <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                <button type="submit" form="addForm" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
            </div>
        </form>
    </div>
</div>

</body>
<style>
    /* Styling agar numbered list & bullet list tetap tampil di tabel */
    .content-html ol {
    list-style-type: decimal;
    margin-left: 20px;
    }

    .content-html ul {
    list-style-type: disc;
    margin-left: 20px;
    }

    .content-html li {
    margin-bottom: 4px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
        //toogle form
        const toggleFormButton = document.getElementById('toggleFormButton');
        const formContainer = document.getElementById('formContainer');

        toggleFormButton.addEventListener('click', () => {
            formContainer.classList.toggle('hidden');
        });

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

    document.addEventListener('DOMContentLoaded', function() {
    let editors = {};

    // Fungsi untuk inisialisasi CKEditor
    function initCKEditor(elementId, inputId) {
        ClassicEditor
            .create(document.querySelector(elementId), {
                toolbar: [
                    'bold', 'italic', '|', 
                    'bulletedList', 'numberedList', '|', 
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                editors[elementId] = editor;
                
                // Set nilai awal jika ada
                let initialValue = document.querySelector(inputId).value;
                editor.setData(initialValue);

                // Update hidden input saat ada perubahan
                editor.model.document.on('change:data', () => {
                    document.querySelector(inputId).value = editor.getData();
                });
            })
            .catch(error => console.error('CKEditor error:', error));
        }

        // Inisialisasi CKEditor di form tambah
        if (document.querySelector('#editor-pekerjaan')) {
            initCKEditor('#editor-pekerjaan', '#pekerjaan-input');
        }
        if (document.querySelector('#editor-kondisi_bulanlalu')) {
            initCKEditor('#editor-kondisi_bulanlalu', '#kondisi_bulanlalu-input');
        }
        if (document.querySelector('#editor-kondisi_bulanini')) {
            initCKEditor('#editor-kondisi_bulanini', '#kondisi_bulanini-input');
        }
        if (document.querySelector('#editor-update')) {
            initCKEditor('#editor-update', '#update-input');
        }
        if (document.querySelector('#editor-rencana_implementasi')) {
            initCKEditor('#editor-rencana_implementasi', '#rencana_implementasi-input');
        }
        if (document.querySelector('#editor-keterangan')) {
            initCKEditor('#editor-keterangan', '#keterangan-input');
        }

        // Inisialisasi CKEditor saat modal edit dibuka
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-target');
                const id = modalId.replace('#editEventModal', '');

                if (document.querySelector(modalId)) {
                    initCKEditor(`#edit-${id}-pekerjaan`, `#edit-${id}-pekerjaan-input`);
                    initCKEditor(`#edit-${id}-kondisi_bulanlalu`, `#edit-${id}-kondisi_bulanlalu-input`);
                    initCKEditor(`#edit-${id}-kondisi_bulanini`, `#edit-${id}-kondisi_bulanini-input`);
                    initCKEditor(`#edit-${id}-update`, `#edit-${id}-update-input`);
                    initCKEditor(`#edit-${id}-rencana_implementasi`, `#edit-${id}-rencana_implementasi-input`);
                    initCKEditor(`#edit-${id}-keterangan`, `#edit-${id}-keterangan-input`);

                }
            });
        });

        // Fungsi untuk menutup modal
        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.fixed');
                modal.classList.add('hidden');
            });
        });
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
                tanggal: cells[0]?.innerText.trim() || '',
                pekerjaan: cells[1]?.innerText.trim() || '',
                kondisi_bulanlalu: cells[2]?.innerText.trim() || '',
                kondisi_bulanini: cells[3]?.innerText.trim() || '',
                update: cells[4]?.innerText.trim() || '',
                rencana_implementasi: cells[5]?.innerText.trim() || '',
                keterangan: cells[6]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal && item.pekerjaan)
        .map(item => `
            <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.pekerjaan}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.kondisi_bulanlalu}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.kondisi_bulanini}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.update}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.rencana_implementasi}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.keterangan}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    try {
        const response = await fetch('/hrga/laporanptbos/export-pdf', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                table: pdfTable
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'laporanptbos.pdf';
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


</script>
</html>