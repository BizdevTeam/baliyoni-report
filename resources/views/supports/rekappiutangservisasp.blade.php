<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Piutang Servis ASP</title>
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
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">Rekap Piutang Servis ASP</h1>

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
                    <div id="pelaksana-container">
                        <div class="pelaksana-item flex items-center space-x-2 mb-2">
                            <select name="pelaksana[]" class="w-full border-gray-300 rounded p-2 pelaksana-select"
                                required>
                                <option value="" disabled selected>Pilih Pelaksana</option>
                                <option value="CV. ARI DISTRIBUTION CENTER">CV. ARI DISTRIBUTION CENTER</option>
                                <option value="CV. BALIYONI COMPUTER">CV. BALIYONI COMPUTER</option>
                                <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                                <option value="CV. ELKA MANDIRI (50%)-SAMITRA">CV. ELKA MANDIRI (50%)-SAMITRA</option>
                                <option value="CV. ELKA MANDIRI (50%)-DETRAN">CV. ELKA MANDIRI (50%)-DETRAN</option>
                            </select>
                            <input type="text" name="nilai_piutang[]" class="w-full border-gray-300 rounded p-2"
                                placeholder="Nilai Piutang" required>
                            <button type="button"
                                class="remove-pelaksana bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </div>
                    </div>
                    <button type="button" id="add-pelaksana" class="bg-green-500 text-white px-4 py-2 rounded">Tambah
                        pelaksana</button>
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
                    <th class="border border-gray-300 px-4 py-2">pelaksana</th>
                    <th class="border border-gray-300 px-4 py-2">Nilai Piutang</th>
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
        document.getElementById('add-pelaksana').addEventListener('click', () => {
            const pelaksanaContainer = document.getElementById('pelaksana-container');

            // Membuat elemen pelaksana dan nilai Piutang
            const newpelaksanaItem = document.createElement('div');
            newpelaksanaItem.className = 'pelaksana-item flex items-center space-x-2 mb-2';

            const pelaksanaSelect = document.createElement('select');
            pelaksanaSelect.name = 'pelaksana[]';
            pelaksanaSelect.className = 'w-full border-gray-300 rounded p-2 pelaksana-select';
            pelaksanaSelect.required = true;

            // Menambahkan opsi default dan pelaksana
            pelaksanaSelect.innerHTML = `
                <option value="" disabled selected>Pilih pelaksana</option>
                    <option value="CV. ARI DISTRIBUTION CENTER">CV. ARI DISTRIBUTION CENTER</option>
                    <option value="CV. BALIYONI COMPUTER">CV. BALIYONI COMPUTER</option>
                    <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                    <option value="CV. ELKA MANDIRI (50%)-SAMITRA">CV. ELKA MANDIRI (50%)-SAMITRA</option>
                    <option value="CV. ELKA MANDIRI (50%)-DETRAN">CV. ELKA MANDIRI (50%)-DETRAN</option>
            `;

            const piutangInput = document.createElement('input');
            piutangInput.type = 'text';
            piutangInput.name = 'nilai_piutang[]';
            piutangInput.className = 'w-full border-gray-300 rounded p-2';
            piutangInput.placeholder = 'Nilai Piutang';
            piutangInput.required = true;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'remove-pelaksana bg-red-500 text-white px-2 py-1 rounded';
            removeButton.textContent = 'Hapus';

            // Menambahkan logika hapus
            removeButton.addEventListener('click', () => {
                newpelaksanaItem.remove();
            });

            // Menambahkan elemen ke container
            newpelaksanaItem.appendChild(pelaksanaSelect);
            newpelaksanaItem.appendChild(piutangInput);
            newpelaksanaItem.appendChild(removeButton);

            pelaksanaContainer.appendChild(newpelaksanaItem);
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
        function isDuplicateEntry(bulanTahun, pelaksanaList, items) {
            const hasDuplicate = new Set(pelaksanaList).size !== pelaksanaList.length;
            if (hasDuplicate) {
                alert('pelaksana yang sama tidak boleh ditambahkan dalam bulan/tahun yang sama.');
                return true;
            }

            return items.some(item => item.bulan_tahun === bulanTahun && pelaksanaList.includes(item.pelaksana));
        }

        // Fetch Existing Data
        async function fetchData() {
            try {
                const response = await fetch('/supports/rekappiutangservisasp/data');
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
            const pelaksanaList = [...document.querySelectorAll('select[name="pelaksana[]"]')].map(select =>
                select.value.trim());
            const nilaiPiutangList = [...document.querySelectorAll('input[name="nilai_piutang[]"]')].map(
                input => parseFloat(input.value.trim()));

            if (!bulanTahun || pelaksanaList.some(p => !p) || nilaiPiutangList.some(isNaN)) {
                alert('Semua kolom harus diisi dengan benar.');
                return;
            }

            const payload = {
                id: editId,
                bulan_tahun: bulanTahun,
                pelaksana: pelaksanaList,
                nilai_piutang: nilaiPiutangList
            };

            const url = editMode ? `/supports/rekappiutangservisasp/update/${editId}` :
                '/supports/rekappiutangservisasp/store';

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
                const response = await fetch(`/supports/rekappiutangservisasp/destroy/${id}`, {
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
            const url = filter ? `/supports/rekappiutangservisasp/data?bulan_tahun=${filter}` :
                '/supports/rekappiutangservisasp/data';

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
                    <td class="border px-4 py-2">${item.pelaksana}</td>
                    <td class="border px-4 py-2">Rp ${item.nilai_piutang.toLocaleString()}</td>
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
            const labels = items.map(item => item.pelaksana);
            const dataValues = items.map(item => item.nilai_piutang);
            const colors = ['#4bc0c0', '#ff6384', '#ffce56', '#36a2eb', '#9966ff'];

            const ctx = chartCanvas.getContext('2d');
            if (window.myChart) window.myChart.destroy();

            window.myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        label: 'Piutang pelaksana',
                        data: dataValues,
                        backgroundColor: colors,
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'pelaksana'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Piutang (Rp)'
                            }
                        },
                    },
                },
            });
        }

        // Edit Data
        function editData(id, data) {
            const parsedData = JSON.parse(decodeURIComponent(data)); // Parse data dari string ke objek
            console.log(parsedData); // Debugging: cek struktur data

            // Pastikan `pelaksana` adalah array
            parsedData.pelaksana = Array.isArray(parsedData.pelaksana) ?
                parsedData.pelaksana :
                (typeof parsedData.pelaksana === 'string' ? parsedData.pelaksana.split(',') : []);

            // Pastikan `nilai_piutang` adalah array
            parsedData.nilai_piutang = Array.isArray(parsedData.nilai_piutang) ?
                parsedData.nilai_piutang :
                (typeof parsedData.nilai_piutang === 'string' ? parsedData.nilai_piutang.split(',').map(Number) : []);

            editMode = true; // Aktifkan mode edit
            editId = id; // Simpan ID data yang sedang diedit

            // Set judul modal
            modalTitle.textContent = 'Edit Data';
            document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;

            // Bersihkan container pelaksana
            const pelaksanaContainer = document.getElementById('pelaksana-container');
            pelaksanaContainer.innerHTML = '';

            // Tambahkan elemen pelaksana dan nilai pendapatan
            parsedData.pelaksana.forEach((pelaksana, index) => {
                const newPelaksanaItem = document.createElement('div');
                newPelaksanaItem.className = 'pelaksana-item flex items-center space-x-2 mb-2';

                const pelaksanaSelect = document.createElement('select');
                pelaksanaSelect.name = 'pelaksana[]';
                pelaksanaSelect.className = 'w-full border-gray-300 rounded p-2 pelaksana-select';
                pelaksanaSelect.required = true;

                pelaksanaSelect.innerHTML = `
            <option value="" disabled>Pilih Pelaksana</option>
            <option value="CV. ARI DISTRIBUTION CENTER">CV. ARI DISTRIBUTION CENTER</option>
            <option value="CV. BALIYONI COMPUTER">CV. BALIYONI COMPUTER</option>
            <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
            <option value="CV. ELKA MANDIRI (50%)-SAMITRA">CV. ELKA MANDIRI (50%)-SAMITRA</option>
            <option value="CV. ELKA MANDIRI (50%)-DETRAN">CV. ELKA MANDIRI (50%)-DETRAN</option>
        `;
                pelaksanaSelect.value = pelaksana; // Set nilai pelaksana

                const nilaiPiutangInput = document.createElement('input');
                nilaiPiutangInput.type = 'text';
                nilaiPiutangInput.name = 'nilai_piutang[]';
                nilaiPiutangInput.className = 'w-full border-gray-300 rounded p-2';
                nilaiPiutangInput.placeholder = 'Nilai Pendapatan';
                nilaiPiutangInput.value = parsedData.nilai_piutang[index] || ''; // Set nilai pendapatan
                nilaiPiutangInput.required = true;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'remove-pelaksana bg-red-500 text-white px-2 py-1 rounded';
                removeButton.textContent = 'Hapus';

                removeButton.addEventListener('click', () => {
                    newPelaksanaItem.remove();
                });

                newPelaksanaItem.appendChild(pelaksanaSelect);
                newPelaksanaItem.appendChild(nilaiPiutangInput);
                newPelaksanaItem.appendChild(removeButton);

                pelaksanaContainer.appendChild(newPelaksanaItem);
            });

            // Tampilkan modal
            modal.classList.remove('hidden');
        }

        // Initial Load
        updateData();
    </script>

</body>

</html>
