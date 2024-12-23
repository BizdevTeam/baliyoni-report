<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kas Hutang Piutang Stok</title>
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
        <x-accountingside class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">Kas Hutang Piutang Stok</h1>
                    <!-- Button Tambah Data -->
                    <button id="open-modal" class="bg-red-600 text-white px-4 py-2 rounded mb-4">Tambah Data</button>


        <!-- Modal -->
        <div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-xl font-bold mb-4" id="modal-title">Tambah Data</h2>
                <form id="modal-form" class="space-y-4">
                    <div>
                        <label for="modal-bulan_tahun" class="block text-sm font-medium">Bulan/Tahun</label>
                        <input type="text" id="modal-bulan_tahun" name="bulan_tahun"
                            class="w-full border-gray-300 rounded p-2" placeholder="mm/yyyy" required>
                    </div>
                    <div>
                        <label for="modal-kas" class="block text-sm font-medium">Kas (RP)</label>
                        <input type="text" id="modal-kas" name="kas" class="w-full border-gray-300 rounded p-2"
                            placeholder="0" min="0" required>
                    </div>
                    <div>
                        <label for="modal-hutang" class="block text-sm font-medium">Hutang (RP)</label>
                        <input type="text" id="modal-hutang" name="hutang"
                            class="w-full border-gray-300 rounded p-2" placeholder="0" min="0" required>
                    </div>
                    <div>
                        <label for="modal-piutang" class="block text-sm font-medium">Piutang (RP)</label>
                        <input type="text" id="modal-piutang" name="piutang"
                            class="w-full border-gray-300 rounded p-2" placeholder="0" min="0" required>
                    </div>
                    <div>
                        <label for="modal-stok" class="block text-sm font-medium">Stok (RP)</label>
                        <input type="text" id="modal-stok" name="stok" class="w-full border-gray-300 rounded p-2"
                            placeholder="0" min="0" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="close-modal"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Batal</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <label for="filter-tahun" class="block text-sm font-medium">Filter Berdasarkan Tahun</label>
            <input type="text" id="filter-tahun" class="border-gray-300 rounded p-2" placeholder="yyyy">
            <button type="button" id="apply-filter" class="bg-red-600 text-white px-4 py-2 rounded">Terapkan
                Filter</button>
        </div>

        <!-- Table -->
        <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Bulan/Tahun</th>
                    <th class="border border-gray-300 px-4 py-2">Kas (RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Hutang(RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Piutang (RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Stok (RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table"></tbody>
        </table>

        <!-- Chart -->
        <div class="mt-6 items-center text-center mx-auto w-[600px]">
            <canvas id="chart"></canvas>
        </div>

    </div>

    <script>
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

        // Submit Form
        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {
                bulan_tahun: document.getElementById('modal-bulan_tahun').value,
                kas: Number(document.getElementById('modal-kas').value),
                hutang: Number(document.getElementById('modal-hutang').value),
                piutang: Number(document.getElementById('modal-piutang').value),
                stok: Number(document.getElementById('modal-stok').value),
            };

            const url = editMode ? `/accounting/kashutangpiutangstok/update/${editId}` :
                '/accounting/kashutangpiutangstok/store';
            const method = editMode ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    await updateData(); // Refresh data
                    modal.classList.add('hidden'); // Hide modal
                } else {
                    alert(result.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Network Error:', error);
                alert('Terjadi kesalahan saat menyimpan data.');
            }
        });

        // Delete Data
        async function deleteData(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

            try {
                const response = await fetch(`/accounting/kashutangpiutangstok/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });

                const result = await response.json();
                if (result.success) {
                    await updateData(); // Refresh data after deletion
                } else {
                    alert(result.message || 'Gagal menghapus data.');
                }
            } catch (error) {
                console.error('Error deleting data:', error);
                alert('Terjadi kesalahan saat menghapus data.');
            }
        }

        // Update Data By Year
        async function updateDataByYear(year) {
            try {
                const response = await fetch(`/accounting/kashutangpiutangstok/filter?tahun=${year}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);

                const result = await response.json();
                if (result.success) {
                    updateTable(result.data);
                    updateChart(result.data);
                } else {
                    alert(result.message || 'Data tidak ditemukan untuk tahun ini.');
                    updateTable([]);
                    updateChart([]);
                }
            } catch (error) {
                console.error('Error fetching data by year:', error.message);
                alert('Terjadi kesalahan saat memuat data.');
            }
        }

        // Apply Filter
        document.getElementById('apply-filter').addEventListener('click', () => {
            const filterYear = document.getElementById('filter-tahun').value.trim();
            if (filterYear.length === 4 && !isNaN(filterYear)) {
                updateDataByYear(filterYear);
            } else {
                alert('Masukkan tahun yang valid (format: yyyy).');
            }
        });

        // Update Data
        async function updateData(filter = '') {
            const url = filter ? `/accounting/kashutangpiutangstok/filter?tahun=${filter}` :
                '/accounting/kashutangpiutangstok/data';
            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    console.log('Data diterima', result.data);

                    const items = result.data;

                    updateTable(result.data);
                    updateChart(result.data);
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
            tableBody.innerHTML = '';

            if (items.length === 0) {
                tableBody.innerHTML =
                    `<tr><td colspan="3" class="text-center py-4">Tidak ada data untuk ditampilkan.</td></tr>`;
                return;
            }

            items.forEach((item) => {
                const row = `
                    <tr class="border-b">
                        <td class="border px-4 py-2">${item.bulan_tahun}</td>
                        <td class="border px-4 py-2">Rp ${item.kas.toLocaleString()}</td>
                        <td class="border px-4 py-2">Rp ${item.hutang.toLocaleString()}</td>
                        <td class="border px-4 py-2">Rp ${item.piutang.toLocaleString()}</td>
                        <td class="border px-4 py-2">Rp ${item.stok.toLocaleString()}</td>
                        <td class="border px-4 py-2 flex items-center justify-center space-x-2">
                            <button onclick="editData(${item.id}, '${encodeURIComponent(JSON.stringify(item))}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </button>
                            <button onclick="deleteData(${item.id})" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </td>
                    </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        }

        //Update Chart
        function updateChart(items) {
            const labels = ['Kas', 'Hutang', 'Piutang', 'Stok'];
            const dataValues = [
                items.reduce((total, item) => total + item.kas, 0),
                items.reduce((total, item) => total + item.hutang, 0),
                items.reduce((total, item) => total + item.piutang, 0),
                items.reduce((total, item) => total + item.stok, 0),
            ];

            // const dataValues = [data.kas, data.hutang, data.piutang, data.stok]; 
            const backgroundColors = [
                'rgba(75, 192, 192, 0.7)', // Warna untuk "Kas"
                'rgba(255, 99, 132, 0.7)', // Warna untuk "Hutang"
                'rgba(54, 162, 235, 0.7)', // Warna untuk "Piutang"
                'rgba(255, 206, 86, 0.7)', // Warna untuk "Stok"
            ];

            const ctx = chartCanvas.getContext('2d');
            if (window.myChart) {
                console.log('Menghapus Chart Lama')
                window.myChart.destroy();
            }

            window.myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        label:  ['Kas', 'Hutang', 'Piutang', 'Stok'],
                        data: dataValues,
                        backgroundColor: backgroundColors,
                        borderWidth: 1,
                    }],
                },
           
            options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                            let value = context.raw;
                            let percentage = ((value / total) * 100).toFixed(2);
                            return `Rp ${value.toLocaleString()} (${percentage}%)`;
                        },
                    },
                },
                datalabels: {
                    color: '#fff',
                    formatter: function(value, context) {
                        let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                        let percentage = ((value / total) * 100).toFixed(2);
                        return `${percentage}%`;
                    },
                    font: {
                        size: 20,
                        weight: 'bold',
                    },
                },
            },
        },
    });

        }

        // Edit Data
        async function editData(id, data) {
            const parsedData = JSON.parse(decodeURIComponent(data));

            editMode = true;
            editId = id;
            modalTitle.textContent = 'Edit Data';

            document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;
            document.getElementById('modal-kas').value = parsedData.kas;
            document.getElementById('modal-hutang').value = parsedData.hutang;
            document.getElementById('modal-piutang').value = parsedData.piutang;
            document.getElementById('modal-stok').value = parsedData.stok;

            modal.classList.remove('hidden');
        }

        // Initial Load
        updateData();
    </script>

</body>

</html>
