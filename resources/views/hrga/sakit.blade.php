<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan HRD (SAKIT)</title>
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
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">Laporan HRD (SAKIT)</h1>

        <!-- Button Tambah Data -->
        <div class="flex gap-4 mb-4">
            <a href="/admin" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Kembali</a>
            <button id="open-modal" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Tambah
                Data</button>
        </div>

        <!-- Modal -->
        <div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-xl font-bold mb-4" id="modal-title">Tambah Data</h2>
                <form id="modal-form" class="space-y-4" method="POST">
                    @csrf <!-- Token CSRF untuk Laravel -->
                    <div>
                        <label for="modal-bulan_tahun" class="block text-sm font-medium">Bulan/Tahun</label>
                        <input type="text" id="modal-bulan_tahun" name="bulan_tahun"
                            class="w-full border-gray-300 rounded p-2" placeholder="mm/yyyy" required>
                    </div>
                    <div id="nama-container">
                        <div class="nama-item flex items-center space-x-2 mb-2">
                            <input type="text" name="nama[]" class="w-full border-gray-300 rounded p-2"
                                placeholder="Nama Karyawan" required>
                            <input type="text" name="total_sakit[]" class="w-full border-gray-300 rounded p-2"
                                placeholder="Total Sakit" required>
                            <button type="button"
                                class="remove-nama bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </div>
                    </div>
                    <button type="button" id="add-nama" class="bg-red-500 text-white px-4 py-2 rounded">Tambah
                        nama</button>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" id="close-modal"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <label for="filter-bulan-tahun" class="block text-sm font-medium">Filter Bulan/Tahun</label>
            <input type="text" id="filter-bulan-tahun" class="border-gray-300 rounded p-2" placeholder="mm/yyyy">
            <button type="button" id="apply-filter" class="bg-red-600 text-white px-4 py-2 rounded">Terapkan
                Filter</button>
        </div>

        <!-- Table -->
        <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Bulan/Tahun</th>
                    <th class="border border-gray-300 px-4 py-2">Nama Karyawan</th>
                    <th class="border border-gray-300 px-4 py-2">Total Sakit</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table"></tbody>
        </table>

        <!-- Chart -->
        <div class="mt-6 items-center text-center mx-auto">
            <canvas id="chart"></canvas>
        </div>

    </div>

    <script>
        document.getElementById('add-nama').addEventListener('click', () => {
            const namaContainer = document.getElementById('nama-container');

            // Membuat elemen nama dan nilai pendapatan
            const newnamaItem = document.createElement('div');
            newnamaItem.className = 'nama-item flex items-center space-x-2 mb-2';
            
            const namaInput = document.createElement('input');
            namaInput.type = 'text';
            namaInput.name = 'nama[]';
            namaInput.className = 'w-full border-gray-300 rounded p-2';
            namaInput.placeholder = 'Nama Karyawan';
            namaInput.required = true;

            const sakitInput = document.createElement('input');
            sakitInput.type = 'text';
            sakitInput.name = 'total_sakit[]';
            sakitInput.className = 'w-full border-gray-300 rounded p-2';
            sakitInput.placeholder = 'Total Sakit';
            sakitInput.required = true;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'remove-nama bg-red-500 text-white px-2 py-1 rounded';
            removeButton.textContent = 'Hapus';

            // Menambahkan logika hapus
            removeButton.addEventListener('click', () => {
                newnamaItem.remove();
            });

            // Menambahkan elemen ke container
            newnamaItem.appendChild(namaInput);
            newnamaItem.appendChild(sakitInput);
            newnamaItem.appendChild(removeButton);

            namaContainer.appendChild(newnamaItem);
        });

        const modal = document.getElementById('modal');
        const openModalButton = document.getElementById('open-modal');
        const closeModalButton = document.getElementById('close-modal');
        const modalForm = document.getElementById('modal-form');
        const modalTitle = document.getElementById('modal-title');
        const chartCanvas = document.getElementById('chart');

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

        // Validate Duplicate Entries
        function isDuplicateEntry(bulanTahun, namaList, items) {
            const hasDuplicate = new Set(namaList).size !== namaList.length;
            if (hasDuplicate) {
                alert('Nama yang sama tidak boleh ditambahkan dalam bulan/tahun yang sama.');
                return true;
            }

            return items.some(item => item.bulan_tahun === bulanTahun && namaList.includes(item.nama));
        }

        // Fetch Existing Data
        async function fetchData() {
            try {
                const response = await fetch('/hrga/sakit/data');
                const result = await response.json();
                return result.success ? result.data : [];
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            }
        }

        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const bulanTahun = document.getElementById('modal-bulan_tahun').value.trim();
            const namaList = [...document.querySelectorAll('input[name="nama[]"]')].map(
                input => input.value.trim());
            const sakitList = [...document.querySelectorAll('input[name="total_sakit[]"]')].map(
                input => parseFloat(input.value.trim()));

            if (!bulanTahun || namaList.some(p => !p) || sakitList.some(isNaN)) {
                alert('Semua kolom harus diisi dengan benar.');
                return;
            }

            const payload = {
                id: editId,
                bulan_tahun: bulanTahun,
                nama: namaList,
                total_sakit: sakitList
            };

            const url = editMode ? `/hrga/sakit/update/${editId}` :
                '/hrga/sakit/store';

            try {
                const response = await fetch(url, {
                    method: editMode ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();
                if (result.success) {
                    alert('Data berhasil disimpan.');
                    modal.classList.add('hidden');
                    updateData();
                } else {
                    alert(result.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data.');
            }
        });

        // Delete Data
        async function deleteData(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

            try {
                const response = await fetch(`/hrga/sakit/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    updateData();
                } else {
                    alert(result.message || 'Gagal menghapus data.');
                }
            } catch (error) {
                console.error('Error deleting data:', error);
                alert('Terjadi kesalahan saat menghapus data.');
            }
        }

        // Filter Data
        document.getElementById('apply-filter').addEventListener('click', () => {
            const filterValue = document.getElementById('filter-bulan-tahun').value;
            updateData(filterValue);
        });

        // Update Data
        async function updateData(filter = '') {
            const url = filter ? `/hrga/sakit/data?bulan_tahun=${filter}` :
                '/hrga/sakit/data';

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    const items = result.data;
                    updateTable(items); // Ensure this updates the table correctly
                    updateChart(items); // Ensure this updates the chart correctly
                } else {
                    alert('Gagal memuat data.');
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                alert('Terjadi kesalahan saat memuat data.');
            }
        }

        // Update Table
        function updateTable(items) {
            const tableBody = document.getElementById('data-table');
            tableBody.innerHTML = ''; // Clear the table before rendering new data

            if (items.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4">No data available</td></tr>';
            } else {
                items.forEach((item) => {
                    const row = `
                <tr class="border-b">
                    <td class="border px-4 py-2">${item.bulan_tahun}</td>
                    <td class="border px-4 py-2">${item.nama}</td>
                    <td class="border px-4 py-2">Rp ${item.total_sakit.toLocaleString()}</td>
                    <td class="border px-4 py-2 flex items-center justify-center space-x-2">
                        <button onclick="editData(${item.id}, '${encodeURIComponent(JSON.stringify(item))}')"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Edit
                        </button>
                        <button onclick="deleteData(${item.id})"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
                            Delete
                        </button>
                    </td>
                </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            }
        }

        // Update Chart
        function updateChart(items) {
                const canvas = document.getElementById('chart');
                if (!canvas) {
                    console.error('Canvas element with ID "chartCanvas" not found.');
                    return;
                }
                const ctx = canvas.getContext('2d');

                if (window.myChart) {
                    window.myChart.destroy();
                }

                const labels = items.map(item => item.nama);
                const dataValues = items.map(item => item.total_sakit);
                const backgroundColors = items.map(() =>
                    `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
                );

                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total Sakit',
                            data: dataValues,
                            backgroundColor: backgroundColors,
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        size: 14,
                                    },
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => `Rp ${context.raw.toLocaleString()}`,
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            }

        // Edit Data
        function editData(id, data) {
            const parsedData = JSON.parse(decodeURIComponent(data)); // Parse data dari string ke objek
            console.log(parsedData); // Debugging: cek struktur data

            // Pastikan `nama` adalah array
            parsedData.nama = Array.isArray(parsedData.nama) ?
                parsedData.nama :
                (typeof parsedData.nama === 'string' ? parsedData.nama.split(',') : []);

            // Pastikan `total_sakit` adalah array
            parsedData.total_sakit = Array.isArray(parsedData.total_sakit) ?
                parsedData.total_sakit :
                (typeof parsedData.total_sakit === 'string' ? parsedData.total_sakit.split(',').map(Number) : []);

            editMode = true; // Aktifkan mode edit
            editId = id; // Simpan ID data yang sedang diedit

            // Set judul modal
            modalTitle.textContent = 'Edit Data';
            document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;

            // Bersihkan container nama
            const namaContainer = document.getElementById('nama-container');
            namaContainer.innerHTML = '';

            // Tambahkan elemen nama dan nilai pendapatan
            parsedData.nama.forEach((nama, index) => {
                const newnamaItem = document.createElement('div');
                newnamaItem.className = 'nama-item flex items-center space-x-2 mb-2';

                const namaInput = document.createElement('input');
                namaInput.type = 'text';
                namaInput.name = 'nama[]';
                namaInput.className = 'w-full border-gray-300 rounded p-2';
                namaInput.placeholder = 'Nama Karyawan';
                namaInput.value = parsedData.nama[index] || ''; // Set nilai pendapatan
                namaInput.required = true;

                const sakitInput = document.createElement('input');
                sakitInput.type = 'text';
                sakitInput.name = 'total_sakit[]';
                sakitInput.className = 'w-full border-gray-300 rounded p-2';
                sakitInput.placeholder = 'Total Sakit';
                sakitInput.value = parsedData.total_sakit[index] || ''; // Set nilai pendapatan
                sakitInput.required = true;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'remove-nama bg-red-500 text-white px-2 py-1 rounded';
                removeButton.textContent = 'Hapus';

                removeButton.addEventListener('click', () => {
                    newnamaItem.remove();
                });

                newnamaItem.appendChild(namaInput);
                newnamaItem.appendChild(sakitInput);
                newnamaItem.appendChild(removeButton);

                namaContainer.appendChild(newnamaItem);
            });

            // Tampilkan modal
            modal.classList.remove('hidden');
        }

        // Initial Load
        updateData();
    </script>

</body>

</html>
