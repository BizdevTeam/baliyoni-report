<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan PPN</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
     @vite('resources/css/app.css')  
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-5">
        <!-- Page Header -->
        <h1 class="text-3xl font-bold mb-5">Laporan SPI TI</h1>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-4">
            <button class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 flex items-center gap-2" data-modal-target="#addEventModal">
                <i class="fa fa-plus"></i>
                Add
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
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">Bulan & Tahun</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Judul</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aspek</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Masalah</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Solusi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Implementasi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporanspitis as $laporanspiti)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->bulan_tahun }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->judul }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->aspek }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->masalah }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->solusi }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspiti->implementasi }}</td>

                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600" data-modal-target="#editEventModal{{ $laporanspiti->id_spiti }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('laporanspiti.destroy', $laporanspiti->id_spiti) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600" onclick="return confirm('Are you sure to delete?')">
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal for Edit Data -->
                        <div class="fixed z-50 overflow-y-auto inset-0 bg-black bg-opacity-50  w-full flex items-center justify-center hidden" id="editEventModal{{ $laporanspiti->id_spiti }}">
                            <div class="bg-white w-[50%] h-[100%] p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('laporanspiti.update', $laporanspiti->id_spiti) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan_tahun" class="block text-sm font-medium">Bulan & Tahun</label>
                                            <input type="month" name="bulan_tahun" class="w-full p-2 border rounded" value="{{ $laporanspiti->bulan_tahun }}" required>
                                        </div>
                                        <div>
                                            <label for="judul" class="block text-sm font-medium">Judul</label>
                                            <textarea name="judul" class="w-full p-2 border rounded" rows="1" required>{{ $laporanspiti->judul }}</textarea>
                                        </div>
                                        <div>
                                            <label for="aspek" class="block text-sm font-medium">Aspek</label>
                                            <textarea name="aspek" class="w-full p-2 border rounded" rows="1" required>{{ $laporanspiti->aspek }}</textarea>
                                        </div>
                                        <div>
                                            <label for="masalah" class="block text-sm font-medium">Masalah</label>
                                            <textarea name="masalah" class="w-full p-2 border rounded" rows="1" required>{{ $laporanspiti->masalah }}</textarea>
                                        </div>
                                        <div>
                                            <label for="solusi" class="block text-sm font-medium">Solusi</label>
                                            <textarea name="solusi" class="w-full p-2 border rounded" rows="1" required>{{ $laporanspiti->solusi }}</textarea>
                                        </div>
                                        <div>
                                            <label for="implementasi" class="block text-sm font-medium">Implementasi</label>
                                            <textarea name="implementasi" class="w-full p-2 border rounded" rows="1" required>{{ $laporanspiti->implementasi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-modal-close>Close</button>
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal untuk Add Data -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('laporanspiti.store') }}">
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
                    <textarea name="masalah" class="w-full p-2 border rounded"></textarea>
                </div>
                <div>
                    <label for="solusi" class="block text-sm font-medium">Solusi</label>
                    <textarea name="solusi" class="w-full p-2 border rounded" rows="3" required></textarea>
                </div>
                <div>
                    <label for="implementasi" class="block text-sm font-medium">Implementasi</label>
                    <input type="text" name="implementasi" class="w-full p-2 border rounded">
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-modal-close>Close</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add</button>
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
