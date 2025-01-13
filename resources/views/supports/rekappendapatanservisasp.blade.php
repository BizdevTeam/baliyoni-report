<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pendapatan Service ASP</title>
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

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Rekap Pendapatan Service ASP</h1>
        <!-- Action Buttons -->
        <div class="flex items-center mb-4 gap-2">
            <form method="GET" action="{{ route('rpsasp.index') }}" class="flex items-center gap-2">
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
        <div class="overflow-x-auto bg-white shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">pelaksana</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Nilai</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rpsasps as $rpsasp)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rpsasp->bulan_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rpsasp->pelaksana }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rpsasp->nilai_formatted }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $rpsasp->id_rpsasp }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('rpsasp.destroy', $rpsasp->id_rpsasp) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 text-white px-3 py-2 rounded" onclick="return confirm('Are you sure to delete?')">
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <!-- Modal for Edit Event -->
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $rpsasp->id_rpsasp }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('rpsasp.update', $rpsasp->id_rpsasp) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                            <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $rpsasp->bulan }}" required>
                                        </div>
                                        <div>
                                            <label for="pelaksana" class="block text-sm font-medium">Pelaksana</label>
                                            <select name="pelaksana" class="w-full p-2 border rounded" required>
                                                <option value="CV. ARI DISTRIBUTION CENTER" {{ $rpsasp->pelaksana == 'CV. ARI DISTRIBUTION CENTER' ? 'selected' : '' }}>CV. ARI DISTRIBUTION CENTER</option>
                                                <option value="CV. BALIYONI COMPUTER" {{ $rpsasp->pelaksana == 'CV. BALIYONI COMPUTER' ? 'selected' : '' }}>CV. BALIYONI COMPUTER</option>
                                                <option value="PT. NABA TECHNOLOGY SOLUTIONS" {{ $rpsasp->pelaksana == 'PT. NABA TECHNOLOGY SOLUTIONS' ? 'selected' : '' }}>PT. NABA TECHNOLOGY SOLUTIONS</option>
                                                <option value="CV. ELKA MANDIRI (50%)-SAMITRA" {{ $rpsasp->pelaksana == 'CV. ELKA MANDIRI (50%)-SAMITRA' ? 'selected' : '' }}>CV. ELKA MANDIRI (50%)-SAMITRA</option>
                                                <option value="CV. ELKA MANDIRI (50%)-DETRAN" {{ $rpsasp->pelaksana == 'CV. ELKA MANDIRI (50%)-DETRAN' ? 'selected' : '' }}>CV. ELKA MANDIRI (50%)-DETRAN</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="nilai_pendapatan" class="block text-sm font-medium">Nilai</label>
                                            <input type="number" name="nilai_pendapatan" class="w-full p-2 border rounded" value="{{ $rpsasp->nilai_pendapatan }}" required>
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
        <div class="m-4">
            {{ $rpsasps->links('pagination::tailwind') }}
        </div>
        </div>
        </div>
        <div class="mx-auto bg-white p-6 mt-3 rounded-lg shadow">
            <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Diagram</h1>
            <div class="mt-6 items-center text-center mx-auto w-[600px]">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Modal untuk Add Event -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('rpsasp.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium">Bulan</label>
                    <input type="month" name="bulan" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="kas_masuk" class="block text-sm font-medium">pelaksana</label>
                    <select name="pelaksana" class="block text-sm font-medium" required>
                        <option value="CV. ARI DISTRIBUTION CENTER">CV. ARI DISTRIBUTION CENTER</option>
                        <option value="CV. BALIYONI COMPUTER">CV. BALIYONI COMPUTER</option>
                        <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                        <option value="CV. ELKA MANDIRI (50%)-SAMITRA">CV. ELKA MANDIRI (50%)-SAMITRA</option>
                        <option value="CV. ELKA MANDIRI (50%)-DETRAN">CV. ELKA MANDIRI (50%)-DETRAN</option>
                    </select>
                </div>
                <div>
                    <label for="nilai_pendapatan" class="block text-sm font-medium">Nilai</label>
                    <input type="number" name="nilai_pendapatan" class="w-full p-2 border rounded" required>
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
</script>
</html>