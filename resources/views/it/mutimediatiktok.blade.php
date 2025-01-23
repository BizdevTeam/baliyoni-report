<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Multimedia Tiktok</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Laporan Multimedia Tiktok</h1>

        <!-- Action Buttons -->
        <div class="flex items-center mb-4 gap-2">
            <form method="GET" action="{{ route('tiktok.index') }}" class="flex items-center gap-2">
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
                                <button class="bg-red-600 text-white px-3 py-2 rounded" data-modal-target="#editEventModal{{ $itmultimediatiktok->id_tiktok }}">
                                    <i class="fa fa-pen"></i>
                                    Edit
                                </button>
                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('tiktok.destroy', $itmultimediatiktok->id_tiktok) }}">
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
                {{ $itmultimediatiktoks->links('pagination::tailwind') }}
            </div>
        </div>
        <button id="export-pdf" class="bg-red-600 text-white px-3 py-2 rounded shadow-md hover:shadow-lg transition duration-300 ease-in-out">
            Export to PDF
        </button>        
    </div>
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
                <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" data-modal-close>Close</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Add</button>
            </div>
        </form>
    </div>
</div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
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
    document.getElementById('export-pdf').addEventListener('click', function () {
    // Salin elemen tabel untuk dimanipulasi
    const tableElement = document.querySelector('.overflow-x-auto table');
    let clonedTable = tableElement.cloneNode(true);

    // Hapus kolom gambar dari tabel duplikat
    clonedTable.querySelectorAll('tr').forEach(row => {
        let imageCell = row.children[1];  // Asumsikan kolom gambar di indeks ke-1
        if (imageCell) {
            row.removeChild(imageCell);
        }
    });

    // Buat konten HTML untuk PDF
    let htmlContent = `
        <div style='width: 100%;'>
            <h2 style='font-size: 14px; text-align: center; margin-bottom: 10px;'>Tabel Data</h2>
            <table style='border-collapse: collapse; width: 100%; font-size: 10px;' border='1'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #000; padding: 1px;'>Bulan</th>
                        <th style='border: 1px solid #000; padding: 2px;'>Total Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody>`;

    clonedTable.querySelectorAll('tbody tr').forEach(row => {
        let bulan = row.children[0].innerText;
        let keterangan = row.children[1].innerText;

        htmlContent += `
            <tr>
                <td style='border: 1px solid #000; padding: 1px; text-align: center;'>${bulan}</td>
                <td style='border: 1px solid #000; padding: 1px;'>${keterangan}</td>
            </tr>`;
    });

    htmlContent += `</tbody>
            </table>
        </div>`;

    // Menambahkan gambar di bawah tabel
    document.querySelectorAll('.overflow-x-auto table tbody tr').forEach(row => {
        let imageElement = row.children[1]?.querySelector('img');
        if (imageElement) {
            htmlContent += `
                <div style='text-align: center; margin-top: 20px;'>
                    <img src='${imageElement.src}' style='width: 100%; height: auto;'>
                </div>`;
        }
    });

    // Header dan Footer
    let headerImagePath = '{{ asset ("images/HEADER.png") }}'; // Sesuaikan path header
    let header = `
        <div style='position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;'>
            <img src='${headerImagePath}' style='width: 100%; height: auto;' />
        </div>
    `;

    let footer = `
        <div style='text-align: center; font-size: 12px; margin-top: 20px;'>
            Dicetak pada: ${new Date().toLocaleDateString()} | Laporan Marketing | Halaman <span class='page'></span> dari <span class='topage'></span>
        </div>
    `;

    // Opsi PDF
    const options = {
        margin: [35, 10, 10, 10], // [top, left, bottom, right]
        filename: 'laporan_rekap_penjualan.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    // Konversi HTML ke PDF dengan header dan footer
    html2pdf().set(options).from(header + htmlContent + footer).save();
});
</script>
</html>
