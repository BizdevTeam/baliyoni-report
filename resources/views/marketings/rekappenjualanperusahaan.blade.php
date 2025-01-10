<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Penjualan Perusahaan</title>
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
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Rekap Penjualan Perusahaan</h1>
        <!-- Action Buttons -->
        <div class="flex items-center mb-4">
            <form method="GET" action="{{ route('rekappenjualanperusahaan.index') }}">
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
                        <th class="border border-gray-300 px-4 py-2 text-center">Perusahaan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Nilai Paket</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekappenjualanperusahaans as $rekappenjualanperusahaan)
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->bulan_formatted }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->perusahaan }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $rekappenjualanperusahaan->total_penjualan_formatted }}</td>
                            <td class="border border-gray-300 py-6 text-center flex justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $rekappenjualanperusahaan->id_rpp }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('rekappenjualanperusahaan.destroy', $rekappenjualanperusahaan->id_rpp) }}">
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
                        <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editEventModal{{ $rekappenjualanperusahaan->id_rpp }}">
                            <div class="bg-white w-1/2 p-6 rounded shadow-lg">
                                <h3 class="text-xl font-semibold mb-4">Edit Data</h3>
                                <form method="POST" action="{{ route('rekappenjualanperusahaan.update', $rekappenjualanperusahaan->id_rpp) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="bulan" class="block text-sm font-medium">Bulan</label>
                                            <input type="month" name="bulan" class="w-full p-2 border rounded" value="{{ $rekappenjualanperusahaan->bulan }}" required>
                                        </div>
                                        <div>
                                            <label for="perusahaan" class="block text-sm font-medium">Pilih Perusahaan</label>
                                            <select name="perusahaan" class="w-full p-2 border rounded" required>
                                                <option value="PT. BALI UNGGUL SEJAHTERA" {{ $rekappenjualanperusahaan->perusahaan == 'PT. BALI UNGGUL SEJAHTERA' ? 'selected' : '' }}>PT. BALI UNGGUL SEJAHTERA</option>
                                                <option value="CV. DANA RASA" {{ $rekappenjualanperusahaan->perusahaan == 'CV. DANA RASA' ? 'selected' : '' }}>CV. DANA RASA</option>
                                                <option value="CV. LAGAAN SAKETI" {{ $rekappenjualanperusahaan->perusahaan == 'CV. LAGAAN SAKETI' ? 'selected' : '' }}>CV. LAGAAN SAKETI</option>
                                                <option value="CV. BALI JAKTI INFORMATIK" {{ $rekappenjualanperusahaan->perusahaan == 'CV. BALI JAKTI INFORMATIK' ? 'selected' : '' }}>CV. BALI JAKTI INFORMATIK</option>
                                                <option value="CV. BALI LINGGA KOMPUTER" {{ $rekappenjualanperusahaan->perusahaan == 'CV. BALI LINGGA KOMPUTER' ? 'selected' : '' }}>CV. BALI LINGGA KOMPUTER</option>
                                                <option value="CV. ARTSOLUTION" {{ $rekappenjualanperusahaan->perusahaan == 'CV. ARTSOLUTION' ? 'selected' : '' }}>CV. ARTSOLUTION</option>
                                                <option value="PT. BALI LINGGA SAKA GUMI" {{ $rekappenjualanperusahaan->perusahaan == 'PT. BALI LINGGA SAKA GUMI' ? 'selected' : '' }}>PT. BALI LINGGA SAKA GUMI</option>
                                                <option value="CV. SAHABAT UTAMA" {{ $rekappenjualanperusahaan->perusahaan == 'CV. SAHABAT UTAMA' ? 'selected' : '' }}>CV. SAHABAT UTAMA</option>
                                                <option value="CV. N & b NET ACCESS" {{ $rekappenjualanperusahaan->perusahaan == 'CV. N & b NET ACCESS' ? 'selected' : '' }}>CV. N & b NET ACCESS</option>
                                                <option value="PT. ELKA SOLUTION NUSANTARA" {{ $rekappenjualanperusahaan->perusahaan == 'PT. ELKA SOLUTION NUSANTARA' ? 'selected' : '' }}>PT. ELKA SOLUTION NUSANTARA</option>
                                                <option value="CV. ARINDAH" {{ $rekappenjualanperusahaan->perusahaan == 'CV. ARINDAH' ? 'selected' : '' }}>CV. ARINDAH</option>
                                                <option value="ARFALINDO" {{ $rekappenjualanperusahaan->perusahaan == 'ARFALINDO' ? 'selected' : '' }}>ARFALINDO</option>                                            
                                            </select>
                                        </div>
                                        <div>
                                            <label for="total_penjualan" class="block text-sm font-medium">Total Penjualan</label>
                                            <input type="number" name="total_penjualan" class="w-full p-2 border rounded" value="{{ $rekappenjualanperusahaan->total_penjualan }}" required>
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
            {{ $rekappenjualanperusahaans->links('pagination::tailwind') }}
        </div>
        </div>
        </div>
        <div class="mx-auto bg-white p-6 mt-3 rounded-lg shadow">
            <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Diagram</h1>
            <div class="mt-6 items-center text-center mx-auto">
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
        <form method="POST" action="{{ route('rekappenjualanperusahaan.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium">Bulan</label>
                    <input type="month" name="bulan" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label for="perusahaan" class="block text-sm font-medium">Pilih Perusahaan</label>
                    <select name="perusahaan" class="w-full p-2 border rounded" required>
                        <option value="PT. BALI UNGGUL SEJAHTERA">PT. BALI UNGGUL SEJAHTERA</option>
                        <option value="CV. DANA RASA">CV. DANA RASA</option>
                        <option value="CV. LAGAAN SAKETI">CV. LAGAAN SAKETI</option>
                        <option value="CV. BALI JAKTI INFORMATIK">CV. BALI JAKTI INFORMATIK</option>
                        <option value="CV. BALI LINGGA KOMPUTER">CV. BALI LINGGA KOMPUTER</option>
                        <option value="CV. ARTSOLUTION">CV. ARTSOLUTION</option>
                        <option value="PT. BALI LINGGA SAKA GUMI">PT. BALI LINGGA SAKA GUMI</option>
                        <option value="CV. SAHABAT UTAMA">CV. SAHABAT UTAMA</option>
                        <option value="CV. N & b NET ACCESS">CV. N & b NET ACCESS</option>
                        <option value="PT. ELKA SOLUTION NUSANTARA">PT. ELKA SOLUTION NUSANTARA</option>
                        <option value="CV. ARINDAH">CV. ARINDAH</option>
                        <option value="ARFALINDO">ARFALINDO</option>
                    </select>
                </div>
                <div>
                    <label for="total_penjualan" class="block text-sm font-medium">Total Penjualan</label>
                    <input type="number" name="total_penjualan" class="w-full p-2 border rounded" required>
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

    var chartData = @json($chartData);

var ctx = document.getElementById('chart').getContext('2d');
var barChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels, // Label bulan
        datasets: chartData.datasets, // Dataset total penjualan
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top', // Posisi legenda
            labels: {
                font :{
                size: 20,
                weight : 'bold',
                    }, //
                }, //
             }, //
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        let value = tooltipItem.raw; // Ambil data nilai
                        return tooltipItem.dataset.text + ' : ' + value.toLocaleString(); // Format angka
                    },
                },
            },
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Perusahaan', // Label sumbu X
                },
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Penjualan (Rp)', // Label sumbu Y
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString(); // Format angka
                    },
                },
            },
        },
    },
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
            bulan: cells[0]?.innerText.trim() || '',
            perusahaan: cells[1]?.innerText.trim() || '',
            total_penjualan: cells[2]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.bulan && item.perusahaan && item.total_penjualan)
        .map(item => `
            <tr>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.bulan}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.perusahaan}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.total_penjualan}</td>
            </tr>
        `).join('');

    const pdfTable = tableContent;

    const chartCanvas = document.querySelector('#chart');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        return;
    }

    const chartBase64 = chartCanvas.toDataURL();

    try {
        const response = await fetch('/marketings/rekappenjualanperusahaan/export-pdf', {
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
            a.download = 'Laporan_rekap_penjualan.pdf';
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