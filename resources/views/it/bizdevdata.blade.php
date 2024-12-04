<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bizdev - {{ $bizdevbulanan->bulan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-5">
        <!-- Page Header -->
        <h1 class="text-3xl font-bold mb-5">Data Bizdev untuk Bulan {{ $bizdevbulanan->bulan }}</h1>

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

        <!-- Success or Error Messages -->
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
                        <th class="border border-gray-300 px-4 py-2 text-center">Aplikasi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Lalu</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Ini</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Update</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Rencana Implementasi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itbizdevdatas as $itbizdevdata)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $itbizdevdata->aplikasi }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itbizdevdata->kondisi_bulanlalu }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itbizdevdata->kondisi_bulanini }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itbizdevdata->update }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itbizdevdata->rencana_implementasi }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $itbizdevdata->keterangan }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600" data-modal-target="#editEventModal{{ $itbizdevdata->id_bizdevdata }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('bizdevdata.destroy', [$bizdevbulanan->id_bizdevbulanan, $itbizdevdata->id_bizdevdata]) }}">
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
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $itbizdevdata->id_bizdevdata }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('bizdevdata.update', [$bizdevbulanan->id_bizdevbulanan, $itbizdevdata->id_bizdevdata]) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <label for="aplikasi" class="block text-sm font-medium">Aplikasi</label>
                                        <input type="text" name="aplikasi" value="{{ old('aplikasi', $itbizdevdata->aplikasi) }}" class="mt-1 block w-full px-3 py-2 border rounded" required>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="kondisi_bulanlalu" class="block text-sm font-medium">Kondisi Bulan Lalu</label>
                                        <input type="text" name="kondisi_bulanlalu" value="{{ old('kondisi_bulanlalu', $itbizdevdata->kondisi_bulanlalu) }}" class="mt-1 block w-full px-3 py-2 border rounded" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="kondisi_bulanini" class="block text-sm font-medium">Kondisi Bulan Ini</label>
                                        <input type="text" name="kondisi_bulanini" value="{{ old('kondisi_bulanini', $itbizdevdata->kondisi_bulanini) }}" class="mt-1 block w-full px-3 py-2 border rounded" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="update" class="block text-sm font-medium">Update</label>
                                        <input type="text" name="update" value="{{ old('update', $itbizdevdata->update) }}" class="mt-1 block w-full px-3 py-2 border rounded" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="rencana_implementasi" class="block text-sm font-medium">Rencana Implementasi</label>
                                        <input type="text" name="rencana_implementasi" value="{{ old('rencana_implementasi', $itbizdevdata->rencana_implementasi) }}" class="mt-1 block w-full px-3 py-2 border rounded" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                                        <textarea name="keterangan" class="mt-1 block w-full px-3 py-2 border rounded">{{ old('keterangan', $itbizdevdata->keterangan) }}</textarea>
                                    </div>

                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Data</button>
                                    <button type="button" class="ml-3 bg-gray-500 text-white px-4 py-2 rounded close-modal" data-modal-target="#editEventModal{{ $itbizdevdata->id_bizdevdata }}">Close</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
        <div class="bg-white w-1/2 p-6 rounded shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
            <form method="POST" action="{{ route('bizdevdata.store', ['bizdevbulanan_id' => $bizdevbulanan->id_bizdevbulanan]) }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="aplikasi" class="block text-sm font-medium">Aplikasi</label>
                        <input type="text" name="aplikasi" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="kondisi_bulanlalu" class="block text-sm font-medium">Kondisi Bulan Lalu</label>
                        <input type="text" name="kondisi_bulanlalu" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="kondisi_bulanini" class="block text-sm font-medium">Kondisi Bulan Ini</label>
                        <input type="text" name="kondisi_bulanini" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="update" class="block text-sm font-medium">Update</label>
                        <input type="text" name="update" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="rencana_implementasi" class="block text-sm font-medium">Rencana Implementasi</label>
                        <input type="text" name="rencana_implementasi" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                        <input type="text" name="keterangan" class="w-full p-2 border rounded">
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
