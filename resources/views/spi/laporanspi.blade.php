<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan SPI Operasional</title>
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
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Laporan SPI Operasional</h1>
        <!-- Action Buttons -->
        <div class="flex items-center mb-4 gap-2">
            <form method="GET" action="{{ route('laporanspi.index') }}" class="flex items-center gap-2">
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
            <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Judul</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aspek</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Masalah</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Solusi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Implementasi</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporanspis as $laporanspi)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->bulan_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->judul }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->aspek }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->masalah }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->solusi }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanspi->implementasi }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $laporanspi->id_spi }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('laporanspi.destroy', $laporanspi->id_spi) }}">
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
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanspi->id_spi }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('laporanspi.update', $laporanspi->id_spi) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                            <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $laporanspi->bulan }}" required>
                                        </div>
                                        <div>
                                            <label for="judul" class="block text-sm font-medium">Judul</label>
                                            <textarea name="judul" class="w-full p-2 border rounded" rows="1"
                                                required>{{ $laporanspi->judul }}</textarea>
                                        </div>
                                        <div>
                                            <label for="aspek" class="block text-sm font-medium">Aspek</label>
                                            <textarea name="aspek" class="w-full p-2 border rounded" rows="1"
                                                required>{{ $laporanspi->aspek }}</textarea>
                                        </div>
                                        <div>
                                            <label for="masalah" class="block text-sm font-medium">Masalah</label>
                                            <textarea name="masalah" class="w-full p-2 border rounded" rows="1"
                                                required>{{ $laporanspi->masalah }}</textarea>
                                        </div>
                                        <div>
                                            <label for="solusi" class="block text-sm font-medium">Solusi</label>
                                            <textarea name="solusi" class="w-full p-2 border rounded" rows="1"
                                                required>{{ $laporanspi->solusi }}</textarea>
                                        </div>
                                        <div>
                                            <label for="implementasi"
                                                class="block text-sm font-medium">Implementasi</label>
                                            <textarea name="implementasi" class="w-full p-2 border rounded" rows="1"
                                                required>{{ $laporanspi->implementasi }}</textarea>
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
            {{ $laporanspis->links('pagination::tailwind') }}
        </div>
        </div>
        <button onclick="exportToPDF()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">
            Ekspor ke PDF
        </button>
        </div>
        </div>
    </div>

    <!-- Modal untuk Add Event -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('laporanspi.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium">Bulan</label>
                    <input type="month" name="bulan" class="w-full p-2 border rounded" required>
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
                    <label for="solusi" class="block text-sm font-medium">Solusi</label>
                    <textarea name="solusi" class="w-full p-2 border rounded" rows="1" required></textarea>
                </div>
                <div>
                    <label for="implementasi" class="block text-sm font-medium">Implementasi</label>
                    <input type="text" name="implementasi" class="w-full p-2 border rounded">
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
                bulan: cells[0]?.innerText.trim() || '',
                judul: cells[1]?.innerText.trim() || '',
                aspek: cells[2] ?.innerText.trim() || '',
                masalah: cells[3]?.innerText.trim() || '',
                solusi: cells[4]?.innerText.trim() || '',
                implementasi: cells[5]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.bulan && item.judul)
        .map(item => `
            <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.bulan}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.judul}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.aspek}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.masalah}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.solusi}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.implementasi}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    try {
        const response = await fetch('/spi/laporanspi/export-pdf', {
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
            a.download = 'laporanspi.pdf';
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


</script>
</html>