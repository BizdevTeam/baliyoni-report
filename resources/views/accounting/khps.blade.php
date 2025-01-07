<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kas Hutang Piutang Stok</title>
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
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Kas Hutang Piutang Stok</h1>
        <!-- Action Buttons -->
        <div class="flex items-center mb-4">
            <form method="GET" action="{{ route('khps.index') }}">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 mr-2 max-w-md">
                    <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                    <button type="submit" class="text-gray-500 focus:outline-none" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m2.85-7.65a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0z" />
                        </svg>
                    </button>
                </div>
            </form>
            
            <button class="bg-red-600 text-white px-4 py-2 rounded shadow flex items-center gap-2" data-modal-target="#addEventModal">
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
                        <th class="border border-gray-300 px-4 py-2 text-center">Kas</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Hutang</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Piutang</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Stok</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kashutangpiutangstoks as $kashutangpiutangstok)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $kashutangpiutangstok->bulan_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $kashutangpiutangstok->kas_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $kashutangpiutangstok->hutang_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $kashutangpiutangstok->piutang_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $kashutangpiutangstok->stok_formatted }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $kashutangpiutangstok->id_khps }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('khps.destroy', $kashutangpiutangstok->id_khps) }}">
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
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $kashutangpiutangstok->id_khps }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('khps.update', $kashutangpiutangstok->id_khps) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                            <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $kashutangpiutangstok->bulan }}" required>
                                        </div>
                                        <div>
                                            <label for="kas" class="block text-sm font-medium">Kas</label>
                                            <input type="number" name="kas" class="w-full p-2 border rounded" value="{{ $kashutangpiutangstok->kas }}" required>
                                        </div>
                                        <div>
                                            <label for="hutang" class="block text-sm font-medium">Hutang</label>
                                            <input type="number" name="hutang" class="w-full p-2 border rounded" value="{{ $kashutangpiutangstok->hutang }}" required>
                                        </div>
                                        <div>
                                            <label for="piutang" class="block text-sm font-medium">Piutang</label>
                                            <input type="number" name="piutang" class="w-full p-2 border rounded" value="{{ $kashutangpiutangstok->piutang }}" required>
                                        </div>
                                        <div>
                                            <label for="stok" class="block text-sm font-medium">Stok</label>
                                            <input type="number" name="stok" class="w-full p-2 border rounded" value="{{ $kashutangpiutangstok->stok }}" required>
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
            {{ $kashutangpiutangstoks->links('pagination::tailwind') }}
        </div>
        </div>
        </div>
        <div class="mx-auto bg-white p-6 mt-3 rounded-lg shadow">
            <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Diagram</h1>
            <div class="mt-6 items-center text-center mx-auto w-[600px]">
                <canvas id="chart"></canvas>
            </div>
            <button onclick="exportToPDF()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Ekspor ke PDF
            </button>
        </div>
    </div>

    <!-- Modal untuk Add Event -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addEventModal">
    <div class="bg-white w-1/2 p-6 rounded shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Add New Data</h3>
        <form method="POST" action="{{ route('khps.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium">Bulan</label>
                    <input type="month" name="bulan" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="kas" class="block text-sm font-medium">Kas</label>
                    <input type="number" name="kas" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="hutang" class="block text-sm font-medium">Hutang</label>
                    <input type="number" name="hutang" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="piutang" class="block text-sm font-medium">Piutang</label>
                    <input type="number" name="piutang" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="stok" class="block text-sm font-medium">Stok</label>
                    <input type="number" name="stok" class="w-full p-2 border rounded" required>
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
    const chartCanvas = document.getElementById('chart');
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

    // Ambil data dari controller
    var chartData = @json($chartData);
        
        // Membuat diagram lingkaran
        var ctx = document.getElementById('chart').getContext('2d');
        var pieChart = new Chart(ctx, {
            type: 'pie', // Jenis chart: pie
            data: chartData, // Data dari controller
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw.toLocaleString(); // Menampilkan data dengan format angka
                            }
                        }
                    }
                }
            }
        });

        async function exportToPDF() {
    // Ambil CSRF Token
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
            kas: cells[1]?.innerText.trim() || '',
            hutang: cells[2]?.innerText.trim() || '',
            piutang: cells[3]?.innerText.trim() || '',
            stok: cells[4]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.bulan && item.kas && item.hutang && item.piutang && item.stok)
        .map(item => `
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${item.bulan}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${item.kas}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${item.hutang}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${item.piutang}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${item.stok}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    const chartCanvas = document.querySelector('#chart');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        return;
    }

    const chartBase64 = chartCanvas.toDataURL();

    // Kirim data tabel dan grafik ke server
    try {
        const response = await fetch('/accounting/khps/export-pdf', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                table: pdfTable,
                chart: chartBase64,
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Laporan_kas_hutang_piutang_stok.pdf';
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