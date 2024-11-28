<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Paket Administrasi</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">


</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Laporan Paket Administrasi</h1>

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
                        <label for="modal-keterangan" class="block text-sm font-medium">Keterangan</label>
                        <input type="text" id="modal-keterangan" name="keterangan"
                            class="w-full border-gray-300 rounded p-2" placeholder="Keterangan (opsional)">
                    </div>
                    <div>
                        <label for="modal-website" class="block text-sm font-medium">Website</label>
                        <select id="modal-website" name="website" class="w-full border-gray-300 rounded p-2" required>
                            <option value="" disabled selected>Pilih Website</option>
                            <option value="E - Katalog">E - Katalog</option>
                            <option value="E - Katalog Luar Bali">E - Katalog Luar Bali</option>
                            <option value="Balimall">Balimall</option>
                            <option value="Siplah">Siplah</option>
                        </select>
                    </div>
                    <div>
                        <label for="modal-paket_rp" class="block text-sm font-medium">Paket (RP)</label>
                        <input type="text" id="modal-paket_rp" name="paket_rp"
                            class="w-full border-gray-300 rounded p-2" placeholder="0" min="0" required>
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
            <input type="text" id="filter-bulan-tahun" class="border-gray-300 rounded p-2" placeholder="mm/yyyy">
            <button type="button" id="apply-filter" class="bg-red-600 text-white px-4 py-2 rounded">Terapkan
                Filter</button>
        </div>

        <!-- Table -->
        <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Bulan/Tahun</th>
                    <th class="border border-gray-300 px-4 py-2">Website</th>
                    <th class="border border-gray-300 px-4 py-2">Paket (RP)</th>
                    <th class="border border-gray-300 px-4 py-2">Keterangan</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table"></tbody>
        </table>

        <!-- Chart -->
        <div class="mt-6">
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
                website: document.getElementById('modal-website').value,
                paket_rp: Number(document.getElementById('modal-paket_rp').value),
                keterangan: document.getElementById('modal-keterangan').value || null,
            };

            const url = editMode ? `/marketings/laporanpaketadministrasi/update/${editId}` :
                '/marketings/laporanpaketadministrasi/store';
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
                const response = await fetch(`/marketings/laporanpaketadministrasi/destroy/${id}`, {
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

        // Fetch and Update Data
        async function updateData(filter = '') {
            const url = filter ? `/marketings/laporanpaketadministrasi/data?bulan_tahun=${filter}` : '/marketings/laporanpaketadministrasi/data';
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    updateTable(data.data);
                    updateChart(data.data);
                } else {
                    alert('Gagal memuat data.');
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Update Table
        function updateTable(items) {
    const tableBody = document.getElementById('data-table');
    tableBody.innerHTML = '';
    items.forEach((item) => {
        const row = `
            <tr class="border-b">
                <td class="border px-4 py-2">${item.bulan_tahun}</td>
                <td class="border px-4 py-2">${item.website}</td>
                <td class="border px-4 py-2">${item.paket_rp}</td>
                <td class="border px-4 py-2">${item.keterangan || '-'}</td>
                <td class="border px-4 py-2 flex items-center justify-center space-x-2">
                    <!-- Edit Button with Icon -->
                    <button onclick="editData(${item.id}, ${JSON.stringify(item)})" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </button>

                    <!-- Delete Button with Icon -->
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
            const labels = items.map((item) => item.website);
            const dataValues = items.map((item) => item.paket_rp);
            const backgroundColors = items.map(() =>
                `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.7)`);

            const ctx = chartCanvas.getContext('2d');
            if (window.myChart) {
                window.myChart.destroy();
            }
            window.myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Paket (RP)',
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
                                    return `Rp ${context.raw.toLocaleString()}`;
                                },
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
            editMode = true;
            editId = id;
            modalTitle.textContent = 'Edit Data';

            document.getElementById('modal-bulan_tahun').value = data.bulan_tahun;
            document.getElementById('modal-website').value = data.website;
            document.getElementById('modal-paket_rp').value = data.paket_rp;
            document.getElementById('modal-keterangan').value = data.keterangan || '';

            modal.classList.remove('hidden');
        }

        // Initial Load
        updateData();
    </script>
</body>

</html>
