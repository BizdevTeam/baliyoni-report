<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan iJASA</title>
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
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Laporan iJASA</h1>
        <!-- Action Buttons -->
        <div class="flex items-center mb-4 gap-2">
            <form method="GET" action="{{ route('laporanijasa.index') }}" class="flex items-center gap-2">
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
                        <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Jam</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Permasalahan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Impact</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Troubleshooting</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Resolve Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Resolve Jam</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporanijasas as $laporanijasa)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanijasa->tanggal_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ \Carbon\Carbon::parse($laporanijasa->jam)->format('h:i A') }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanijasa->permasalahan }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanijasa->impact }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanijasa->troubleshooting }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $laporanijasa->resolve_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ \Carbon\Carbon::parse($laporanijasa->resolve_jam)->format('h:i A') }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $laporanijasa->id_ijasa }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('laporanijasa.destroy', $laporanijasa->id_ijasa) }}">
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
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $laporanijasa->id_ijasa }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('laporanijasa.update', $laporanijasa->id_ijasa) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                                            <input type="date" name="tanggal" class="w-full p-2 border rounded" value="{{ $laporanijasa->tanggal }}" required>
                                        </div>
                                        <div>
                                            <label for="jam" class="block text-sm font-medium">Jam</label>
                                            <input type="time" name="jam" class="w-full p-2 border rounded" value="{{ $laporanijasa->jam }}" required>
                                        </div>
                                        <div>
                                            <label for="permasalahan" class="block text-sm font-medium">Permasalahan</label>
                                            <input type="text" name="permasalahan" class="w-full p-2 border rounded" value="{{ $laporanijasa->permasalahan }}" required>
                                        </div>
                                        <div>
                                            <label for="impact" class="block text-sm font-medium">Impact</label>
                                            <input type="text" name="impact" class="w-full p-2 border rounded" value="{{ $laporanijasa->impact }}" required>
                                        </div>
                                        <div>
                                            <label for="troubleshooting" class="block text-sm font-medium">Troubleshooting</label>
                                            <input type="text" name="troubleshooting" class="w-full p-2 border rounded" value="{{ $laporanijasa->troubleshooting }}" required>
                                        </div>
                                        <div>
                                            <label for="resolve_tanggal" class="block text-sm font-medium">Resolve Tanggal</label>
                                            <input type="date" name="resolve_tanggal" class="w-full p-2 border rounded" value="{{ $laporanijasa->resolve_tanggal }}" required>
                                        </div>
                                        <div>
                                            <label for="resolve_jam" class="block text-sm font-medium">Resolve Jam</label>
                                            <input type="time" name="resolve_jam" class="w-full p-2 border rounded" value="{{ $laporanijasa->resolve_jam }}" required>
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
            {{ $laporanijasas->links('pagination::tailwind') }}
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
        <form method="POST" action="{{ route('laporanijasa.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                    <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="jam" class="block text-sm font-medium">Jam</label>
                    <input type="time" name="jam" class="w-full p-2 border rounded" value="{{ old('jam') }}" required>
                </div>
                <div>
                    <label for="permasalahan" class="block text-sm font-medium">Permasalahan</label>
                    <input type="text" name="permasalahan" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="impact" class="block text-sm font-medium">Impact</label>
                    <input type="text" name="impact" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="troubleshooting" class="block text-sm font-medium">Trobleshooting</label>
                    <input type="text" name="troubleshooting" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="resolve_tanggal" class="block text-sm font-medium">Resolve Tanggal</label>
                    <input type="date" name="resolve_tanggal" class="w-full p-2 border rounded" value="{{ old('resolve_jam') }}" required>
                </div>
                <div>
                    <label for="resolve_jam" class="block text-sm font-medium">Resolve Jam</label>
                    <input type="time" name="resolve_jam" class="w-full p-2 border rounded" required>
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
                jam: cells[1]?.innerText.trim() || '',
                permasalahan: cells[2]?.innerText.trim() || '',
                impact: cells[3]?.innerText.trim() || '',
                troubleshooting: cells[4]?.innerText.trim() || '',
                resolve_tanggal: cells[5]?.innerText.trim() || '',
                resolve_jam: cells[6]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal && item.jam)
        .map(item => `
            <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.jam}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.permasalahan}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.impact}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.troubleshooting}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.resolve_tanggal}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.resolve_jam}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    try {
        const response = await fetch('/hrga/laporanijasa/export-pdf', {
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
            a.download = 'laporanijasa.pdf';
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