<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Arus Kas</title>
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
        <x-adminnav class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">Laporan Arus Kas</h1>
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
                        <label for="modal-kas-masuk" class="block text-sm font-medium">Kas Masuk (RP)</label>
                        <input type="text" id="modal-kas-masuk" name="kas_masuk"
                            class="w-full border-gray-300 rounded p-2" placeholder="0" min="0" required>
                    </div>
                    <div>
                        <label for="modal-kas-keluar" class="block text-sm font-medium">Kas Keluar (RP)</label>
                        <input type="text" id="modal-kas-keluar" name="kas_keluar"
                            class="w-full border-gray-300 rounded p-2" placeholder="0" min="0" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="close-modal"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                        <button type="submit" id="save-data"
                            class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
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
                    <th class="border border-gray-300 px-4 py-2">Kas Masuk (RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Kas Keluar (RP)</th>
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
                kas_masuk: Number(document.getElementById('modal-kas-masuk').value),
                kas_keluar: Number(document.getElementById('modal-kas-keluar').value),
            };

            const url = editMode ? `/accounting/aruskas/update/${editId}` :
                '/accounting/aruskas/store';
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
                    updateData(); // Refresh data after saving
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
            try {
                const response = await fetch(`/accounting/aruskas/destroy/${id}`, {
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

        // Apply Filter
        document.getElementById('apply-filter').addEventListener('click', () => {
            const filterValue = document.getElementById('filter-bulan-tahun').value;
            updateData(filterValue);
        });

        async function updateData(filter = '') {
            const url = filter ? `/accounting/aruskas/data?bulan_tahun=${filter}` :
                '/accounting/aruskas/data';
            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    const items = result.data; // Data untuk tabel dan grafik

                    updateTable(items); // Perbarui tabel
                    updateChart(items); // Perbarui chart
                } else {
                    alert('Gagal memuat data.');
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                alert('Terjadi kesalahan saat memuat data.');
            }
        }

        function updateTable(items, totalPaket = 0) {
            const tableBody = document.getElementById('data-table');
            tableBody.innerHTML = ''; // Bersihkan tabel sebelum merender ulang

            // Render data item per baris
            items.forEach((item) => {
                const row = `
        <tr class="border-b">
            <td class="border px-4 py-2">${item.bulan_tahun}</td>
            <td class="border px-4 py-2">Rp ${item.kas_masuk.toLocaleString()}</td>
            <td class="border px-4 py-2">Rp ${item.kas_keluar.toLocaleString()}</td>
            <td class="border px-4 py-2 flex items-center justify-center space-x-2">
                <button onclick="editData(${item.id}, '${encodeURIComponent(JSON.stringify(item))}')"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </button>
                <button onclick="deleteData(${item.id})" 
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </td>
        </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        }



        // Update Chart
        function updateChart(items) {
            const labels = ['Kas Nasuk','Kas Keluar'];
            const dataValues = [
                items.reduce((total, item) => total + item.kas_masuk, 0),
                items.reduce((total, item) => total + item.kas_keluar, 0),
            ];

            // const dataValues = [data.kas, data.hutang, data.piutang, data.stok]; 
            const backgroundColors = [
                'rgba(75, 192, 192, 0.7)', // Warna untuk "Kas"
                'rgba(255, 99, 132, 0.7)', // Warna untuk "Hutang"
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
                        label: ['Kas Masuk', 'Kas Keluar'],
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
        function editData(id, data) {
            const parsedData = JSON.parse(decodeURIComponent(data)); // Parse JSON string

            editMode = true; // Enable edit mode
            editId = id; // Save the ID being edited
            modalTitle.textContent = 'Edit Data'; // Update modal title

            // Populate modal fields with existing data
            document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;
            document.getElementById('modal-kas-masuk').value = parsedData.kas_masuk;
            document.getElementById('modal-kas-keluar').value = parsedData.kas_keluar;

            modal.classList.remove('hidden'); // Show modal
        }

        // Initial Data Load
        updateData();
    </script>
</body>

</html>
