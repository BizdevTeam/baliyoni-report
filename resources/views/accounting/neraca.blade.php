<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Neraca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            <h1 class="flex text-4xl font-bold text-red-600 justify-center mt-4">Laporan Neraca</h1>

            <div class="flex items-center justify-end transition-all duration-500 mt-8 mb-4">
                <!-- Search -->
                <form method="GET" action="{{ route('neraca.index') }}" class="flex items-center gap-2">
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
                <div class="overflow-x-auto border-l-4 border-red-600 shadow-md rounded-sm">
                    <table class="table-auto w-full border-collapse text-gray-700 text-sm">
                        <thead class="bg-gradient-to-r from-gray-200 to-gray-300 text-gray-900 font-semibold">
                            <tr class="font-sans">
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">Tanggal</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">Thumbnail</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">File Excel</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">Keterangan</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanneracas as $laporanneraca)
                            <tr class="even:bg-gray-50 odd:bg-white hover:bg-gray-100 transition duration-300">
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">{{ $laporanneraca->date_formatted }}</td>
                                <td class="px-6 py-4 text-center text-pretty border-b overflow-hidden " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">
                                    <div class="relative hover:scale-[1.5] transition-transform duration-300">
                                        @if ($laporanneraca->gambar)
                                        <img src="{{ asset('images/accounting/neraca/' . $laporanneraca->gambar) }}" alt="Eror Image" class=" shadow-md hover:shadow-xl rounded-md transition-shadow duration-300 h-16 mx-auto object-cover cursor-pointer" onclick="openModal('{{ asset('images/accounting/neraca/' . $laporanneraca->gambar) }}')">
                                        @else
                                        <img src="{{ asset('images/no_image.png') }}" alt="Default Image" class=" shadow-md hover:shadow-xl rounded-md transition-shadow duration-300 h-16 mx-auto object-cover cursor-pointer" onclick="openModal('{{ asset('images/no_image.png') }}')">
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">
                                    @if ($laporanneraca->file_excel)
                                    <a href="{{ asset('files/accounting/neraca/' . $laporanneraca->file_excel) }}"
                                        class="text-blue-600 underline hover:text-blue-800"
                                        download>
                                        Unduh File Excel
                                    </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">{{ $laporanneraca->keterangan }}</td>
                                    <td class="px-6 py-8 text-center flex justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button class="transition duration-300 ease-in-out transform hover:scale-125 flex items-center gap-2 p-2" data-modal-target="#editEventModal{{ $laporanneraca->id_neraca }}">
                                            <i class="fa fa-pen text-red-600" data-aos="fade-right" data-aos-duration="600" data-aos-easing="ease-in-out"></i>
                                        </button>                                                                           
                                    
                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('neraca.destroy', $laporanneraca->id_neraca) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="transition duration-300 ease-in-out transform hover:scale-125 flex items-center p-2" onclick="return confirm('Are you sure to delete?')">
                                                <i class="fa fa-trash text-red-600" data-aos="fade-right" data-aos-duration="600" data-aos-easing="ease-in-out"></i>
                                            </button>
                                        </form>
                                    </td>
                            </tr>

                            <!-- Modal for Edit Event -->
                            <div class="fixed z-50 overflow-y-auto inset-0 backdrop-blur-sm bg-black bg-opacity-70 w-full flex items-center justify-center hidden" id="editEventModal{{ $laporanneraca->id_neraca }}">
                                <div class="bg-white w-[30%] h-[90%] p-6 rounded shadow-lg animate-slide-down transform transition-transform duration-500 ease-out">
                                    <h3 class="text-lg font-semibold mb-3">Edit Data</h3>
                                    <form method="POST" action="{{ route('neraca.update', $laporanneraca->id_neraca) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-3">
                                            <div>
                                                <label for="date" class="block text-sm font-medium">Tanggal</label>
                                                <input type="date" name="date" class="w-full p-2 border rounded" value="{{ $laporanneraca->date }}" required>
                                            </div>
                                            <div>
                                                <label for="gambar" class="block text-sm font-medium">Thumbnail</label>
                                                <input type="file" name="gambar" class="w-full p-2 border rounded">
                                                <div class="mt-2">
                                                    <img src="images/accounting/neraca/{{ $laporanneraca->gambar }}" alt="Event Image" class="h-16">
                                                </div>
                                            </div>
                                            <div>
                                                <label for="file_excel" class="block text-sm font-medium">File Excel</label>
                                                <input type="file" name="file_excel" class="w-full p-2 border rounded">
                                                <div class="mt-2">
                                                    <a href="{{ asset('files/accounting/neraca/' . $laporanneraca->file_excel) }}"
                                                        class="text-blue-600 underline hover:text-blue-800">
                                                        Unduh File Excel
                                                    </a>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                                                <textarea name="keterangan" class="w-full p-2 border rounded" rows="3" required>{{ $laporanneraca->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end gap-2">
                                            <button type="button" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" data-modal-close>Close</button>
                                            <button type="submit" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
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
                                {{ $laporanneracas->links('pagination::tailwind') }}
                            </div>
                        </div>
                </div>
                <div x-data="{ open: false }" class="flex justify-end max-w-md ml-auto p-4">
                    <!-- Tombol untuk membuka modal -->
                    <button @click="open = true"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><mask id="lineMdCloudAltPrintFilledLoop0"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/><set fill="freeze" attributeName="opacity" begin="0.7s" to="0"/></path><g fill="#fff" stroke="none" opacity="0"><circle cx="12" cy="10" r="6"><animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12"/></circle><rect width="9" height="8" x="8" y="12"/><rect width="15" height="12" x="1" y="8" rx="6"><animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="1;0;1;2;1"/></rect><rect width="13" height="10" x="10" y="10" rx="5"><animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="10;9;10;11;10"/></rect><set fill="freeze" attributeName="opacity" begin="0.7s" to="1"/></g><g fill="#000" fill-opacity="0" stroke="none"><circle cx="12" cy="10" r="4"><animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12"/></circle><rect width="9" height="6" x="8" y="12"/><rect width="11" height="8" x="3" y="10" rx="4"><animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="3;2;3;4;3"/></rect><rect width="9" height="6" x="12" y="12" rx="3"><animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="12;11;12;13;12"/></rect><set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1"/><animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0"/></g><g stroke="none"><path fill="#fff" d="M6 11h12v0h-12z"><animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s" values="M6 11h12v0h-12z;M6 11h12v11h-12z"/></path><path fill="#000" d="M8 13h8v0h-8z"><animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s" values="M8 13h8v0h-8z;M8 13h8v7h-8z"/></path><path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z"><animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1"/><animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2" repeatCount="indefinite"/></path></g></g></mask><rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)"/></svg>
                    </button>
                
                    <!-- Modal -->
                    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                        <div class="bg-white p-6 rounded-lg w-96 relative">
                            <!-- Tombol close -->
                            <button @click="open = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                                ✖
                            </button>
                
                            <h2 class="text-xl font-semibold text-red-600 mb-4 text-center">Export Laporan</h2>
                
                            <form action="{{ route('neraca.exportPDF') }}" method="POST">
                                @csrf
                                <label for="date" class="block text-gray-700 font-medium mb-2 text-center">Pilih Tanggal:</label>
                                <input type="date" id="date" name="date" required
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-red-500">
                                
                                <button type="submit"
                                    class="w-full mt-3 bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                                    Export PDF
                                </button>
                            </form>
                
                            @if(session('error'))
                                <p class="mt-3 text-sm text-red-600 text-center">{{ session('error') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal untuk Add Event -->
            <div class="fixed z-50 overflow-y-auto inset-0 backdrop-blur-sm bg-black bg-opacity-70 w-full flex items-center justify-center hidden" id="addEventModal">
                <div class="bg-white w-[30%] h-[80%] p-6 rounded shadow-lg animate-slide-down transform transition-transform duration-500 ease-out">
                    <h3 class="text-lg font-semibold mb-3">Add New Data</h3>
                    <form method="POST" action="{{ route('neraca.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="date" class="block text-sm font-medium">Tanggal</label>
                                <input type="date" name="date" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label for="gambar" class="block text-sm font-medium">Gambar</label>
                                <input type="file" name="gambar" class="w-full p-2 border rounded">
                            </div>
                            <div>
                                <label for="file_excel" class="block text-sm font-medium">File Excel</label>
                                <input type="file" name="file_excel" class="w-full p-2 border rounded">
                            </div>
                            <div>
                                <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                                <textarea name="keterangan" class="w-full p-2 border rounded" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-end gap-2">
                            <button type="button" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm " data-modal-close>Close</button>
                            <button type="submit" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- modal for image -->
            <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden justify-center items-center z-50">
    <div class="relative">
        <img id="modalImage" src="" alt="Full Image" class=" max-w-full max-h-[90vh] rounded-md shadow-lg">
        <button onclick="closeModal()" class="absolute top-2 right-2 bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white p-3 rounded-full shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm">✖</button>
    </div>
</div>

</body>
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
            // Tambahkan kelas animasi sebelum menyembunyikan modal
            modal.classList.add('closing');

            // Tunggu hingga animasi selesai, kemudian sembunyikan modal
            setTimeout(() => {
                modal.classList.add('hidden'); // Menyembunyikan modal
                modal.classList.remove('closing'); // Menghapus kelas animasi
            }, 500); // Durasi animasi (sesuaikan dengan durasi animasi CSS)
        });
    }); 

//modal img
function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

</html>