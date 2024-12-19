<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>iJasa</title>
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
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-adminside class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-adminnav class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4 " />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">iJASA</h1>
                    <!-- Button Tambah Data -->
                    <button id="open-modal" class="bg-red-600 text-white px-4 py-2 rounded mb-4">Tambah Data</button>

        <!-- Button Tambah Data -->
        <button id="open-modal" class="bg-red-600 text-white px-4 py-2 rounded mb-4">Tambah Data</button>

        <!-- Modal -->
        <div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-xl font-bold mb-4" id="modal-title">Tambah Data</h2>
                <form id="modal-form" class="space-y-4">
                    <div>
                        <label for="modal-tanggal" class="block text-sm font-medium">Tanggal</label>
                        <input type="month" id="modal-tanggal" name="tanggal"
                            class="w-full border-gray-300 rounded p-2" required>
                    </div>
                    <div>
                        <label for="modal-jam" class="block text-sm font-medium">Jam</label>
                        <input type="time" id="modal-jam" name="jam" class="w-full border-gray-300 rounded p-2"
                            required>
                    </div>
                    <div>
                        <label for="modal-permasalahan" class="block text-sm font-medium">Permasalahan</label>
                        <input type="text" id="modal-permasalahan" name="permasalahan"
                            class="w-full border-gray-300 rounded p-2" placeholder="Masukkan Permasalahan">
                    </div>
                    <div>
                        <label for="modal-impact" class="block text-sm font-medium">Impact</label>
                        <input type="text" id="modal-impact" name="impact"
                            class="w-full border-gray-300 rounded p-2" placeholder="Masukkan Impact">
                    </div>
                    <div>
                        <label for="modal-troubleshooting" class="block text-sm font-medium">Troubleshooting</label>
                        <input type="text" id="modal-troubleshooting" name="troubleshooting"
                            class="w-full border-gray-300 rounded p-2" placeholder="Masukkan Troubleshooting">
                    </div>
                    <div>
                        <label for="modal-resolve_tanggal" class="block text-sm font-medium text-red-500">Tanggal
                            Resolusi</label>
                        <input type="date" id="modal-resolve_tanggal" name="resolve_tanggal"
                            class="w-full border-gray-300 rounded p-2" required>
                    </div>
                    <div>
                        <label for="modal-resolve_jam" class="block text-sm font-medium text-red-500">Jam
                            Resolusi</label>
                        <input type="time" id="modal-resolve_jam" name="resolve_jam"
                            class="w-full border-gray-300 rounded p-2" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="close-modal"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                        <button type="submit" id="save-data"
                            class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <label for="filter-bulan-tahun" class="block text-sm font-medium">Filter Bulan/Tahun</label>
            <input type="month" id="filter-bulan-tahun" class="border-gray-300 rounded p-2">
            <button type="button" id="apply-filter" class="bg-red-600 text-white px-4 py-2 rounded">Terapkan
                Filter</button>
        </div>

        <!-- Table -->
        <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                    <th class="border border-gray-300 px-4 py-2">Jam</th>
                    <th class="border border-gray-300 px-4 py-2">Permasalahan</th>
                    <th class="border border-gray-300 px-4 py-2">Impact</th>
                    <th class="border border-gray-300 px-4 py-2">Troubleshooting</th>
                    <th class="border border-gray-300 px-4 py-2">Tanggal Resolusi</th>
                    <th class="border border-gray-300 px-4 py-2">Jam Resolusi</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table"></tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal');
            const openModalButton = document.getElementById('open-modal');
            const closeModalButton = document.getElementById('close-modal');
            const modalForm = document.getElementById('modal-form');
            const modalTitle = document.getElementById('modal-title');

            let editMode = false;
            let editId = null;

            // Open Modal
            openModalButton.addEventListener('click', () => {
                modalForm.reset();
                modalTitle.textContent = 'Tambah Data';
                editMode = false;
                modal.classList.remove('hidden');
            });

            // Close Modal
            closeModalButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            // Submit Form
            modalForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const data = {
                    tanggal: document.getElementById('modal-tanggal').value,
                    jam: document.getElementById('modal-jam').value,
                    permasalahan: document.getElementById('modal-permasalahan').value,
                    impact: document.getElementById('modal-impact').value,
                    troubleshooting: document.getElementById('modal-troubleshooting').value,
                    resolve_tanggal: document.getElementById('modal-resolve_tanggal').value,
                    resolve_jam: document.getElementById('modal-resolve_jam').value,
                };

                const url = editMode ? `/hrga/laporanijasa/update/${editId}` :
                    '/hrga/laporanijasa/store';
                const method = editMode ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data),
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert(result.message || 'Data berhasil disimpan.');
                        updateData();
                        modal.classList.add('hidden');
                    } else {
                        alert(result.message || 'Gagal menyimpan data.');
                    }
                } catch (error) {
                    console.error('Network Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                }
            });

            // Delete Data
            window.deleteData = async function(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

                try {
                    const response = await fetch(`/hrga/laporanijasa/destroy/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                        },
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert(result.message || 'Data berhasil dihapus.');
                        updateData();
                    } else {
                        alert(result.message || 'Gagal menghapus data.');
                    }
                } catch (error) {
                    console.error('Error deleting data:', error);
                }
            };

            // Filter Data
            document.getElementById('apply-filter').addEventListener('click', () => {
                const filterValue = document.getElementById('filter-bulan-tahun').value;
                updateData(filterValue);
            });

            // Fetch and Update Data
            async function updateData(filter = '') {
                const url = filter ? `/hrga/laporanijasa/data?bulan_tahun=${filter}` :
                    '/hrga/laporanijasa/data';

                try {
                    const response = await fetch(url);
                    const result = await response.json();
                    const table = document.getElementById('data-table');
                    table.innerHTML = '';

                    result.data.forEach((item) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="border px-4 py-2">${item.tanggal}</td>
                            <td class="border px-4 py-2">${item.jam}</td>
                            <td class="border px-4 py-2">${item.permasalahan}</td>
                            <td class="border px-4 py-2">${item.impact}</td>
                            <td class="border px-4 py-2">${item.troubleshooting}</td>
                            <td class="border px-4 py-2">${item.resolve_tanggal}</td>
                            <td class="border px-4 py-2">${item.resolve_jam}</td>
                            <td class="border px-4 py-2">
                                <button onclick="editData(${item.id}, '${encodeURIComponent(JSON.stringify(item))}')" class="bg-red-500 text-white px-2 py-1 rounded">Edit</button>
                                <button onclick="deleteData(${item.id})" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                            </td>
                        `;
                        table.appendChild(row);
                    });
                } catch (error) {
                    console.error('Error fetching data:', error);
                }
            }

            window.editData = function(id, encodedData) {
                const data = JSON.parse(decodeURIComponent(encodedData));

                editMode = true;
                editId = id;
                modalTitle.textContent = 'Edit Data';

                document.getElementById('modal-tanggal').value = data.tanggal;
                document.getElementById('modal-jam').value = data.jam;
                document.getElementById('modal-permasalahan').value = data.permasalahan;
                document.getElementById('modal-impact').value = data.impact;
                document.getElementById('modal-troubleshooting').value = data.troubleshooting;
                document.getElementById('modal-resolve_tanggal').value = data.resolve_tanggal;
                document.getElementById('modal-resolve_jam').value = data.resolve_jam;

                modal.classList.remove('hidden');
            };

            // Initial Data Load
            updateData();
        });
    </script>
</body>

</html>
