<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan SPI - IT</title>
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
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
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
                x-init="setTimeout(() => showSuccess = false, 5000); setTimeout(() => showError = false, 5000);"
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
        <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Laporan SPI IT</h1>

        <div class="flex items-center justify-end transition-all duration-500 mt-8 p-4">
            <!-- Search -->
            <form method="GET" action="{{ route('laporanspiti.index') }}" class="flex items-center gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input type="date" name="search" placeholder="Search by MM / YYYY" value="{{ request('search') }}"
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
            <div class="mx-auto bg-white p-6 rounded-lg shadow">

        <!-- Event Table -->
        <div class="overflow-x-auto bg-white shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aspek</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Masalah</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Solusi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Implementasi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporanspitis as $laporanspiti)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->tanggal_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 content-html align-top text-justify">{!! $laporanspiti->aspek !!}</td>
                            <td class="border border-gray-300 px-4 py-2 content-html align-top text-justify">{!! $laporanspiti->masalah !!}</td>
                            <td class="border border-gray-300 px-4 py-2 content-html align-top text-justify">{!! $laporanspiti->solusi !!}</td>
                            <td class="border border-gray-300 px-4 py-2 content-html align-top text-justify">{!! $laporanspiti->implementasi !!}</td>
                            <td class="border border-gray-300 py-4 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <!-- Edit Button -->
                                    <button class="text-red-600 hover:text-red-800 transition" 
                                        data-modal-target="#editEventModal{{ $laporanspiti->id_spiti }}">
                                        <i class="fa fa-pen text-lg"></i>
                                    </button>
                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('laporanspiti.destroy', $laporanspiti->id_spiti) }}">
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
    
            @foreach ($laporanspitis as $laporanspiti)
            <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanspiti->id_spiti }}">
                <div class="bg-white w-2/3 p-6 rounded shadow-lg max-h-[80vh] overflow-y-auto">
                    <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                    <form method="POST" action="{{ route('laporanspiti.update', $laporanspiti->id_spiti) }}" enctype="multipart/form-data" id="editForm{{ $laporanspiti->id_spiti }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                                <input type="date" name="tanggal" id="edit-{{ $laporanspiti->id_spiti }}-tanggal-input" value="{{ $laporanspiti->tanggal }}" class="w-full p-2 border rounded">                           
                            </div>
                            <div>
                                <label for="aspek" class="block text-sm font-medium">Aspek</label>
                                <input type="hidden" name="aspek" id="edit-{{ $laporanspiti->id_spiti }}-aspek-input" value="{{ $laporanspiti->aspek }}">
                                <div id="edit-{{ $laporanspiti->id_spiti }}-aspek"></div>
                            </div>
                            <div>
                                <label for="masalah" class="block text-sm font-medium">Masalah</label>
                                <input type="hidden" name="masalah" id="edit-{{ $laporanspiti->id_spiti }}-masalah-input" value="{{ $laporanspiti->masalah }}">
                                <div id="edit-{{ $laporanspiti->id_spiti }}-masalah"></div>
                            </div>
                            <!-- Repeat for other fields -->
                            <div>
                                <label for="solusi" class="block text-sm font-medium mb-1">Solusi <span class="text-red-500">*</span></label>
                                <input type="hidden" name="solusi" id="edit-{{ $laporanspiti->id_spiti }}-solusi-input" value="{{ $laporanspiti->solusi }}" required>
                                <div id="edit-{{ $laporanspiti->id_spiti }}-solusi"></div>
                                <div class="text-red-500 text-sm mt-1 hidden" id="edit-solusi-error-{{ $laporanspiti->id_spiti }}">This field is required</div>
                            </div>
                            <!-- Repeat for other fields -->
                            <div>
                                <label for="implementasi" class="block text-sm font-medium mb-1">Implementasi <span class="text-red-500">*</span></label>
                                <input type="hidden" name="implementasi" id="edit-{{ $laporanspiti->id_spiti }}-implementasi-input" value="{{ $laporanspiti->implementasi }}" required>
                                <div id="edit-{{ $laporanspiti->id_spiti }}-implementasi"></div>
                                <div class="text-red-500 text-sm mt-1 hidden" id="edit-implementasi-error-{{ $laporanspiti->id_spiti }}">This field is required</div>
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
            {{ $laporanspitis->links('pagination::tailwind') }}
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

    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
        <div class="bg-white w-2/3 p-6 rounded shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
            <form method="POST" action="{{ route('laporanspiti.store') }}" enctype="multipart/form-data" id="addForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                        <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label for="aspek" class="block text-sm font-medium">Aspek</label>
                        <input type="hidden" name="aspek" id="aspek-input">
                        <div id="editor-aspek"></div>
                    </div>
                    <div>
                        <label for="masalah" class="block text-sm font-medium">Masalah</label>
                        <input type="hidden" name="masalah" id="masalah-input">
                        <div id="editor-masalah"></div>
                    </div>
                    <div>
                        <label for="solusi" class="block text-sm font-medium mb-1">Solusi <span class="text-red-500">*</span></label>
                        <input type="hidden" name="solusi" id="solusi-input" required>
                        <div id="editor-solusi"></div>
                        <div class="text-red-500 text-sm mt-1 hidden" id="solusi-error">This field is required</div>
                    </div>
                    <div>
                        <label for="implementasi" class="block text-sm font-medium">Implementasi</label>
                        <input type="hidden" name="implementasi" id="implementasi-input">
                        <div id="editor-implementasi"></div>
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
                aspek: cells[1]?.innerText.trim() || '',
                masalah: cells[2]?.innerText.trim() || '',
                solusi: cells[3]?.innerText.trim() || '',
                implementasi: cells[4]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal && item.aspek && item.masalah && item.solusi && item.implementasi)
        .map(item => `
            <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.aspek}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.masalah}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.solusi}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.implementasi}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    try {
        const response = await fetch('/spi/laporanspiti/export-pdf', {
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
            a.download = 'laporanspiti.pdf';
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
    if (document.querySelector('#editor-aspek')) {
        initCKEditor('#editor-aspek', '#aspek-input');
    }
    if (document.querySelector('#editor-masalah')) {
        initCKEditor('#editor-masalah', '#masalah-input');
    }
    if (document.querySelector('#editor-solusi')) {
        initCKEditor('#editor-solusi', '#solusi-input');
    }
    if (document.querySelector('#editor-implementasi')) {
        initCKEditor('#editor-implementasi', '#implementasi-input');
    }

    // Inisialisasi CKEditor saat modal edit dibuka
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const id = modalId.replace('#editEventModal', '');

            if (document.querySelector(modalId)) {
                initCKEditor(`#edit-${id}-aspek`, `#edit-${id}-aspek-input`);
                initCKEditor(`#edit-${id}-masalah`, `#edit-${id}-masalah-input`);
                initCKEditor(`#edit-${id}-solusi`, `#edit-${id}-solusi-input`);
                initCKEditor(`#edit-${id}-implementasi`, `#edit-${id}-implementasi-input`);
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


</script>
</html>