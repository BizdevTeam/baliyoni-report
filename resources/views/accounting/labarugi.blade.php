<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
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
    <link rel="stylesheet" href="{{ asset('css/custom2.css') }}">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/css/custom2.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="mx-auto bg-white/70  p-6 rounded-lg shadow-lg ">
                <h1 class="text-3xl font-bold text-red-600 mb-2 font-montserrat">Laba Rugi</h1>
                <!-- Action Buttons -->
                <div class="flex items-center mb-4 gap-2">
                    <form method="GET" action="{{ route('labarugi.index') }}" class="flex items-center gap-2">
                        <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                            <input 
                                type="month" 
                                name="search" 
                                placeholder="Search by Month" 
                                value="{{ request('search') }}" 
                                class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" 
                            />
                        </div>
                        <button type="submit" class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" aria-label="Search">
                            Search
                        </button>
                    </form>
                    <button class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" data-modal-target="#addEventModal">
                        Add New
                    </button>
                </div>
                <!-- Success Message -->
                @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <!-- Event Table -->
                <div class="overflow-x-auto border-l-4 border-red-600 shadow-md rounded-sm">
                    <table class="table-auto w-full border-collapse text-gray-700 text-sm">
                        <thead class="bg-gradient-to-r from-gray-200 to-gray-300 text-gray-900 font-semibold">
                            <tr class="font-sans">
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">Bulan</th>
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
                            @foreach ($laporanlabarugis as $laporanlabarugi)
                            <tr class="even:bg-gray-50 odd:bg-white hover:bg-gray-100 transition duration-300">
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">{{ $laporanlabarugi->bulan_formatted }}</td>
                                <td class="px-6 py-4 text-center text-pretty border-b overflow-hidden " data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">
                                    <div class="relative hover:scale-[1.5] transition-transform duration-300">
                                        @if ($laporanlabarugi->gambar)
                                        <img src="{{ asset('images/accounting/labarugi/' . $laporanlabarugi->gambar) }}" alt="Eror Image" class=" shadow-md hover:shadow-xl rounded-md transition-shadow duration-300 h-16 mx-auto object-cover cursor-pointer" onclick="openModal('{{ asset('images/accounting/labarugi/' . $laporanlabarugi->gambar) }}')">
                                        @else
                                        <img src="{{ asset('images/no_image.png') }}" alt="Default Image" class=" shadow-md hover:shadow-xl rounded-md transition-shadow duration-300 h-16 mx-auto object-cover cursor-pointer" onclick="openModal('{{ asset('images/no_image.png') }}')">
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">
                                    @if ($laporanlabarugi->file_excel)
                                    <a href="{{ asset('files/accounting/labarugi/' . $laporanlabarugi->file_excel) }}"
                                        class="text-blue-600 underline hover:text-blue-800"
                                        download>
                                        Unduh File Excel
                                    </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-pretty border-b" data-aos="fade-right"
                                    data-aos-duration="400"
                                    data-aos-easing="ease-out-sine">{{ $laporanlabarugi->keterangan }}</td>
                                    <td class="px-6 py-8 text-center flex justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button class="transition duration-300 ease-in-out transform hover:scale-125 flex items-center gap-2 p-2" data-modal-target="#editEventModal{{ $laporanlabarugi->id_labarugi }}">
                                            <i class="fa fa-pen text-red-600" data-aos="fade-right" data-aos-duration="600" data-aos-easing="ease-in-out"></i>
                                        </button>                                                                           
                                    
                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('labarugi.destroy', $laporanlabarugi->id_labarugi) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="transition duration-300 ease-in-out transform hover:scale-125 flex items-center p-2" onclick="return confirm('Are you sure to delete?')">
                                                <i class="fa fa-trash text-red-600" data-aos="fade-right" data-aos-duration="600" data-aos-easing="ease-in-out"></i>
                                            </button>
                                        </form>
                                    </td>
                            </tr>

                            <!-- Modal for Edit Event -->
                            <div class="fixed z-50 overflow-y-auto inset-0 backdrop-blur-sm bg-black bg-opacity-70 w-full flex items-center justify-center hidden" id="editEventModal{{ $laporanlabarugi->id_labarugi }}">
                                <div class="bg-white w-[30%] h-[90%] p-6 rounded shadow-lg animate-slide-down transform transition-transform duration-500 ease-out">
                                    <h3 class="text-lg font-semibold mb-3">Edit Data</h3>
                                    <form method="POST" action="{{ route('labarugi.update', $laporanlabarugi->id_labarugi) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-3">
                                            <div>
                                                <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                                <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $laporanlabarugi->bulan }}" required>
                                            </div>
                                            <div>
                                                <label for="gambar" class="block text-sm font-medium">Thumbnail</label>
                                                <input type="file" name="gambar" class="w-full p-2 border rounded">
                                                <div class="mt-2">
                                                    <img src="images/accounting/labarugi/{{ $laporanlabarugi->gambar }}" alt="Event Image" class="h-16">
                                                </div>
                                            </div>
                                            <div>
                                                <label for="file_excel" class="block text-sm font-medium">File Excel</label>
                                                <input type="file" name="file_excel" class="w-full p-2 border rounded">
                                                <div class="mt-2">
                                                    <a href="{{ asset('files/accounting/labarugi/' . $laporanlabarugi->file_excel) }}"
                                                        class="text-blue-600 underline hover:text-blue-800">
                                                        Unduh File Excel
                                                    </a>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                                                <textarea name="keterangan" class="w-full p-2 border rounded" rows="3" required>{{ $laporanlabarugi->keterangan }}</textarea>
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
                    <div class="m-4">
                        {{ $laporanlabarugis->links('pagination::tailwind') }}
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
                
                            <form action="{{ route('labarugi.exportPDF') }}" method="POST">
                                @csrf
                                <label for="bulan" class="block text-gray-700 font-medium mb-2 text-center">Pilih Bulan:</label>
                                <input type="month" id="bulan" name="bulan" required
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
                    <form method="POST" action="{{ route('labarugi.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                <input type="month" name="bulan" class="w-full p-2 border rounded" required>
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
        <img id="modalImage" src="" alt="Full Image" class="max-w-2xl max-h-[500px] rounded-md shadow-lg">
        <button onclick="closeModal()" class="absolute top-2 right-2 bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white p-3 rounded-full shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm">✖</button>
    </div>
</div>

</body>
<script>
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