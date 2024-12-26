<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan SPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</head>

<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar
            class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="mx-auto bg-white/70  p-6 rounded-lg shadow-lg ">
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Laporan SPI</h1>

                <h1 class="text-sm mb-4 text-black font-lato">Laporan per Bulan</h1>

                <div class="flex justify-end gap-2 mb-4"  data-aos="fade-left" data-aos-anchor-placement="center-center">
                    <!-- Tombol Back -->
                    

                    <!-- Tombol Add New -->
                    <button
                        class="bg-gradient-to-r font-medium  from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm"
                        data-modal-target="#addEventModal" >
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

                <!-- Data Table -->
                <div class="overflow-x-auto border-l-4 border-red-600 shadow-md rounded-sm"  >
                    <table class="table-auto w-full border-collapse text-gray-700 text-sm">
                        <thead class="bg-gradient-to-r from-gray-200 to-gray-300 text-gray-900 font-semibold ">
                            <tr class="font-sans">
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Bulan & Tahun</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Judul</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Aspek</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Masalah</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Solusi</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Implementasi</th>
                                <th class="px-6 py-3 text-center border-b " data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out" data-aos-anchor-placement="center-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporans as $laporan)

                            <tr class="even:bg-gray-50 odd:bg-white hover:bg-gray-100 transition duration-300 ">
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->bulan_tahun }}</td>
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->judul }}</td>
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->aspek }}</td>
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->masalah }}</td>
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->solusi }}</td>
                                <td class="px-6 py-4 text-center border-b" data-aos="fade-right"  data-aos-easing="ease-in-out"  data-aos-duration="400">{{ $laporan->implementasi }}</td>

                                <td class="px-6 py-4 text-center border-b flex justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button
                                        class="  transition duration-300 ease-in-out transform hover:scale-125 flex items-center gap-2 p-2"
                                        data-modal-target="#editEventModal{{ $laporan->id_spi }}" >
                                        <i class="fa fa-pen text-red-600" data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out"></i>

                                    </button>
                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('laporanspi.destroy', $laporan->id_spi) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="  transition duration-300 ease-in-out transform hover:scale-125  flex items-center p-2"
                                            onclick="return confirm('Are you sure to delete?')" >
                                            <i class="fa fa-trash text-red-600" data-aos="fade-right" data-aos-duration="400" data-aos-easing="ease-in-out"></i>

                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal for Edit Data -->
                            <div class="fixed z-50 overflow-y-auto inset-0 backdrop-blur-sm bg-black bg-opacity-70 w-full flex items-center justify-center hidden" 
                                id="editEventModal{{ $laporan->id_spi }}">
                                <div class="bg-white w-[30%] h-[90%] p-6 rounded shadow-lg animate-slide-down transform transition-transform duration-500 ease-out" >
                                    <h3 class="text-lg font-semibold mb-3">Edit Data</h3>
                                    <form method="POST" action="{{ route('laporanspi.update', $laporan->id_spi) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-3">
                                            <div>
                                                <label for="bulan_tahun" class="block text-sm font-medium">Bulan &
                                                    Tahun</label>
                                                <input type="month" name="bulan_tahun" class="w-full p-2 border rounded"
                                                    value="{{ $laporan->bulan_tahun }}" required>
                                            </div>
                                            <div>
                                                <label for="judul" class="block text-sm font-medium">Judul</label>
                                                <textarea name="judul" class="w-full p-2 border rounded" rows="1"
                                                    required>{{ $laporan->judul }}</textarea>
                                            </div>
                                            <div>
                                                <label for="aspek" class="block text-sm font-medium">Aspek</label>
                                                <textarea name="aspek" class="w-full p-2 border rounded" rows="1"
                                                    required>{{ $laporan->aspek }}</textarea>
                                            </div>
                                            <div>
                                                <label for="masalah" class="block text-sm font-medium">Masalah</label>
                                                <textarea name="masalah" class="w-full p-2 border rounded" rows="1"
                                                    required>{{ $laporan->masalah }}</textarea>
                                            </div>
                                            <div>
                                                <label for="solusi" class="block text-sm font-medium">Solusi</label>
                                                <textarea name="solusi" class="w-full p-2 border rounded" rows="1"
                                                    required>{{ $laporan->solusi }}</textarea>
                                            </div>
                                            <div>
                                                <label for="implementasi"
                                                    class="block text-sm font-medium">Implementasi</label>
                                                <textarea name="implementasi" class="w-full p-2 border rounded" rows="1"
                                                    required>{{ $laporan->implementasi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end gap-2">
                                            <button type="button"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-sm "
                                                data-modal-close>Close</button>
                                            <button type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-sm">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk Add Data -->
    <div class="fixed z-50 overflow-y-auto inset-0 backdrop-blur-sm bg-black bg-opacity-70 w-full flex items-center justify-center hidden" id="addEventModal">
        <div class="bg-white w-[30%] h-[92%] p-6 rounded shadow-lg animate-slide-down transform transition-transform duration-500 ease-out">
            <h3 class="text-lg font-semibold mb-3">Add New Data</h3>
            <form method="POST" action="{{ route('laporanspi.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="bulan_tahun" class="block text-sm font-medium">Bulan & Tahun</label>
                        <input type="month" name="bulan_tahun" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label for="judul" class="block text-sm font-medium">Judul</label>
                        <input type="text" name="judul" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="aspek" class="block text-sm font-medium">Aspek</label>
                        <input type="text" name="aspek" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="masalah" class="block text-sm font-medium">Masalah</label>
                        <textarea name="masalah" class="w-full p-2 border rounded" rows="1"></textarea>
                    </div>
                    <div>
                        <label for="masalah" class="block text-sm font-medium">Solusi</label>
                        <textarea name="masalah" class="w-full p-2 border rounded" rows="1" required></textarea>
                    </div>
                    <div>
                        <label for="implementasi" class="block text-sm font-medium">Implementasi</label>
                        <input type="text" name="implementasi" class="w-full p-2 border rounded">
                    </div>
                </div>
                <div class="mt-2 flex justify-end gap-2">
                    <button type="button" class="bg-red-600 text-white px-3 py-1 rounded"
                        data-modal-close>Close</button>
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Add</button>
                </div>
            </form>
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

</script>

</html>