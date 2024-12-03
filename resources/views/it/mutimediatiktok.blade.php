<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Multimedia Tiktok</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-5">
        <!-- Page Header -->
        <h1 class="text-3xl font-bold mb-5">Multimedia Tiktok</h1>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-4">
            <button class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 flex items-center gap-2" onclick="location.reload();">
                <i class="bi bi-arrow-repeat"></i>
                Refresh
            </button>
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

        <!-- Event Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Gambar</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itmultimediatiktoks as $key => $itmultimediatiktok)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $itmultimediatiktok->bulan_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                @if ($itmultimediatiktok->gambar)
                                    <img src="{{ asset('images/it/multimediatiktok/' . $itmultimediatiktok->gambar) }}" alt="Eror Image" class="h-16 mx-auto">
                                @else
                                    <img src="{{ asset('images/no_image.png') }}" alt="Default Image" class="h-16 mx-auto">
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itmultimediatiktok->keterangan }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600" data-modal-target="#editEventModal{{ $itmultimediatiktok->id_tiktok }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('tiktok.destroy', $itmultimediatiktok->id_tiktok) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600" onclick="return confirm('Are you sure to delete?')">
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal for Edit Event -->
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $itmultimediatiktok->id_tiktok }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('tiktok.update', $itmultimediatiktok->id_tiktok) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                            <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $itmultimediatiktok->bulan }}" required>
                                        </div>
                                        <div>
                                            <label for="gambar" class="block text-sm font-medium">Gambar</label>
                                            <input type="file" name="gambar" class="w-full p-2 border rounded">
                                            <div class="mt-2">
                                                <img src="{{ asset('images/it/multimediatiktok/' . $itmultimediatiktok->gambar) }}" alt="Event Image" class="h-16">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                                            <textarea name="keterangan" class="w-full p-2 border rounded" rows="3" required>{{ $itmultimediatiktok->keterangan }}</textarea>
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
    <!-- Modal untuk Add Event -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('tiktok.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium">Bulan</label>
                    <input type="month" name="bulan" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="gambar" class="block text-sm font-medium">Gambar</label>
                    <input type="file" name="gambar" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                    <textarea name="keterangan" class="w-full p-2 border rounded" rows="3" required></textarea>
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
