<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Pembelian (Holding)</title>
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
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-bold text-red-600 mb-2 font-montserrat">Laporan Pembelian (Holding)</h1>
                <!-- Button Tambah Data -->
                <button id="open-modal" class="bg-red-600 text-white px-4 py-2 rounded mb-4">Tambah Data</button>

                <!-- Modal -->
                <div id="modal"
                    class="hidden fixed inset-0 bg-gray-800 bg-opacity-70 flex justify-center items-center"
                    style="z-index: 9999;"> <!-- Set z-index tinggi -->
                    <div class="bg-white p-6 rounded shadow w-full max-w-md" style="z-index: 10000;">
                        <!-- Pastikan ini lebih tinggi -->
                        <h2 class="text-xl font-bold mb-4" id="modal-title">Tambah Data</h2>
                        <form id="modal-form" class="space-y-4" method="POST">
                            @csrf <!-- Token CSRF untuk Laravel -->
                            <div>
                                <label for="modal-bulan_tahun" class="block text-sm font-medium">Bulan/Tahun</label>
                                <input type="text" id="modal-bulan_tahun" name="bulan_tahun"
                                    class="w-full border-gray-300 rounded p-2" placeholder="mm/yyyy" required>
                            </div>
                            <div id="perusahaan-container">
                                <div class="perusahaan-item flex items-center space-x-2 mb-2">
                                    <select name="perusahaan[]"
                                        class="w-full border-gray-300 rounded p-2 perusahaan-select" required>
                                        <option value="" disabled selected>Pilih Perusahaan</option>
                                        <option value="PT. BALIYONI SAGUNA">PT. BALIYONI SAGUNA</option>
                                        <option value="CV. ELKA MANDIRI">CV. ELKA MANDIRI</option>
                                        <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                                        <option value="PT. DWI SRIKANDI INDONESIA">PT. DWI SRIKANDI INDONESIA</option>
                                        <option value="CV. BHIRMA TEKNIK">CV. BHIMA TEKNIK</option>
                                        <option value="PT. DWI SRIKANDI NUSANTARA">PT. DWI SRIKANDI NUSANTARA</option>
                                    </select>
                                    <input type="text" name="nilai[]" class="w-full border-gray-300 rounded p-2"
                                        placeholder="Nilai Holding" required>
                                    <button type="button"
                                        class="remove-perusahaan bg-red-600 text-white px-2 py-1 rounded">Hapus</button>
                                </div>
                            </div>
                            <button type="button" id="add-perusahaan"
                                class="bg-red-600 text-white px-4 py-2 rounded">Tambah
                                perusahaan</button>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" id="close-modal"
                                    class="bg-red-600 text-white px-4 py-2 rounded">Batal</button>
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="mb-4">
                    <label for="filter-bulan-tahun" class="block text-sm font-medium">Filter Bulan/Tahun</label>
                    <input type="text" id="filter-bulan-tahun" class="border-gray-300 rounded p-2"
                        placeholder="mm/yyyy">
                    <button type="button" id="apply-filter" class="bg-red-600 text-white px-4 py-2 rounded">Terapkan
                        Filter</button>
                </div>

                <!-- Table -->
                <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Bulan/Tahun</th>
                            <th class="border border-gray-300 px-4 py-2">perusahaan</th>
                            <th class="border border-gray-300 px-4 py-2">Nilai Holding</th>
                            <th class="border border-gray-300 px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="data-table"></tbody>
                </table>
                <div id="pagination-container" class="flex justify-center mt-4"></div>
                <!-- Chart -->
                <div class="mt-6 items-center text-center mx-auto">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('add-perusahaan').addEventListener('click', () => {
                const perusahaanContainer = document.getElementById('perusahaan-container');

                // Membuat elemen perusahaan dan nilai paket
                const newperusahaanItem = document.createElement('div');
                newperusahaanItem.className = 'perusahaan-item flex items-center space-x-2 mb-2';

                const perusahaanSelect = document.createElement('select');
                perusahaanSelect.name = 'perusahaan[]';
                perusahaanSelect.className = 'w-full border-gray-300 rounded p-2 perusahaan-select';
                perusahaanSelect.required = true;

                // Menambahkan opsi default dan perusahaan
                perusahaanSelect.innerHTML =
                    `
              <option value="" disabled selected>Pilih Perusahaan</option>
                                        <option value="PT. BALIYONI SAGUNA">PT. BALIYONI SAGUNA</option>
                                        <option value="CV. ELKA MANDIRI">CV. ELKA MANDIRI</option>
                                        <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                                        <option value="PT. DWI SRIKANDI INDONESIA">PT. DWI SRIKANDI INDONESIA</option>
                                        <option value="CV. BHIRMA TEKNIK">CV. BHIMA TEKNIK</option>
                                        <option value="PT. DWI SRIKANDI NUSANTARA">PT. DWI SRIKANDI NUSANTARA</option>           `;
                const paketInput = document.createElement('input');
                paketInput.type = 'text';
                paketInput.name = 'nilai[]';
                paketInput.className = 'w-full border-gray-300 rounded p-2';
                paketInput.placeholder = 'Nilai Holding';
                paketInput.required = true;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'remove-perusahaan bg-red-600 text-white px-2 py-1 rounded';
                removeButton.textContent = 'Hapus';

                // Menambahkan logika hapus
                removeButton.addEventListener('click', () => {
                    newperusahaanItem.remove();
                });

                // Menambahkan elemen ke container
                newperusahaanItem.appendChild(perusahaanSelect);
                newperusahaanItem.appendChild(paketInput);
                newperusahaanItem.appendChild(removeButton);

                perusahaanContainer.appendChild(newperusahaanItem);
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
            function isDuplicateEntry(bulanTahun, perusahaanList, items) {
                const hasDuplicate = new Set(perusahaanList).size !== perusahaanList.length;
                if (hasDuplicate) {
                    alert('perusahaan yang sama tidak boleh ditambahkan dalam bulan/tahun yang sama.');
                    return true;
                }

                return items.some(item => item.bulan_tahun === bulanTahun && perusahaanList.includes(item.perusahaan));
            }

            // Fetch Existing Data
            async function fetchData() {
                try {
                    const response = await fetch('/procurements/laporanpembelianholding/data');
                    console.log('Response Status:', response.status);
                    console.log('Response Content:', await response.text());
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
                const perusahaanList = [...document.querySelectorAll('select[name="perusahaan[]"]')].map(select =>
                    select.value.trim());
                const nilaiPaketList = [...document.querySelectorAll('input[name="nilai[]"]')].map(
                    input => parseFloat(input.value.trim()));

                if (!bulanTahun || perusahaanList.some(p => !p) || nilaiPaketList.some(isNaN)) {
                    alert('Semua kolom harus diisi dengan benar.');
                    return;
                }

                const payload = {
                    id: editId,
                    bulan_tahun: bulanTahun,
                    perusahaan: perusahaanList,
                    nilai: nilaiPaketList
                };

                const url = editMode ? `/procurements/laporanpembelianholding/update/${editId}` :
                    '/procurements/laporanpembelianholding/store';

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
                    const response = await fetch(`/procurements/laporanpembelianholding/destroy/${id}`, {
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
                const url = filter ? `/procurements/laporanpembelianholding/data?bulan_tahun=${filter}` :
                    '/procurements/laporanpembelianholding/data';

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
            let currentPage = 1;
            let itemsPerPage = 12; // Maksimal 12 item per halaman
            let filteredItems = []; // Data yang difilter berdasarkan bulan dan tahun

            function updateTable(items, bulanTahun = null) {
                const tableBody = document.getElementById('data-table');
                const paginationContainer = document.getElementById('pagination-container');

                // Filter data berdasarkan bulan dan tahun jika parameter `bulanTahun` diberikan
                if (bulanTahun) {
                    filteredItems = items.filter(item => item.bulan_tahun === bulanTahun);
                } else {
                    filteredItems = items; // Semua data jika tidak ada filter bulan/tahun
                }

                const totalItems = filteredItems.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage);

                // Pastikan halaman tetap dalam rentang yang valid
                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }
                if (currentPage < 1) {
                    currentPage = 1;
                }

                // Hitung data yang akan ditampilkan berdasarkan halaman
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const paginatedItems = filteredItems.slice(startIndex, endIndex);

                // Bersihkan tabel sebelum mengisi data baru
                tableBody.innerHTML = '';

                // Jika tidak ada data yang sesuai
                if (paginatedItems.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4">No data available</td></tr>';
                } else {
                    paginatedItems.forEach((item) => {
                        const row = `
            <tr class="border-b">
                <td class="border px-4 py-2">${item.bulan_tahun}</td>
                <td class="border px-4 py-2">${item.perusahaan}</td>
                <td class="border px-4 py-2">Rp ${item.nilai.toLocaleString()}</td>
                <td class="border px-4 py-2 flex items-center justify-center space-x-2">
                  <button onclick="editData(${item.id}, '${encodeURIComponent(JSON.stringify(item))}')"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </button>
                <button onclick="deleteData(${item.id})" 
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                </td>
            </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                }

                // Buat tombol pagination
                paginationContainer.innerHTML = `
        <button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage('prev')" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
            Previous
        </button>
        <span class="px-4">Page ${currentPage} of ${totalPages}</span>
        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage('next')" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}">
            Next
        </button>`;
            }

            function changePage(direction) {
                if (direction === 'prev' && currentPage > 1) {
                    currentPage--;
                } else if (direction === 'next' && currentPage * itemsPerPage < filteredItems.length) {
                    currentPage++;
                }
                updateTable(filteredItems);
            }


            // Update Chart
            function updateChart(items) {
                const labels = items.map((item) => item.perusahaan); // Label dari nama perusahaan
                const dataValues = items.map((item) => item.nilai); // Nilai paket
                const backgroundColors = items.map(() =>
                    `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.7)`); // Warna acak

                const ctx = chartCanvas.getContext('2d');

                // Hapus chart lama jika ada
                if (window.myChart) {
                    window.myChart.destroy();
                }

                // Buat chart baru
                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'PENJUALAN PERUSAHAAN',
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
                                        size: 20, // Ukuran font label
                                        weight: 'bold', // Tebal tulisan
                                    },
                                },
                            },
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
                const parsedData = JSON.parse(decodeURIComponent(data)); // Parse data dari string ke objek
                console.log(parsedData); // Debugging: cek struktur data

                // Pastikan `perusahaan` adalah array
                const perusahaanArray = Array.isArray(parsedData.perusahaan) ?
                    parsedData.perusahaan : [parsedData.perusahaan]; // Ubah menjadi array jika hanya satu perusahaan

                // Pastikan `nilai` adalah array
                const nilaiPaketArray = Array.isArray(parsedData.nilai) ?
                    parsedData.nilai : [parsedData.nilai]; // Ubah menjadi array jika hanya satu nilai

                editMode = true; // Aktifkan mode edit
                editId = id; // Simpan ID data yang sedang diedit

                // Set judul modal
                modalTitle.textContent = 'Edit Data';
                document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;

                // Bersihkan container perusahaan
                const perusahaanContainer = document.getElementById('perusahaan-container');
                perusahaanContainer.innerHTML = '';

                // Tambahkan elemen perusahaan dan nilai paket
                perusahaanArray.forEach((perusahaan, index) => {
                    const newperusahaanItem = document.createElement('div');
                    newperusahaanItem.className = 'perusahaan-item flex items-center space-x-2 mb-2';

                    // Dropdown untuk perusahaan
                    const perusahaanSelect = document.createElement('select');
                    perusahaanSelect.name = 'perusahaan[]';
                    perusahaanSelect.className = 'w-full border-gray-300 rounded p-2 perusahaan-select';
                    perusahaanSelect.required = true;

                    perusahaanSelect.innerHTML =
                    `
                    <option value="" disabled selected>Pilih Perusahaan</option>
                                        <option value="PT. BALIYONI SAGUNA">PT. BALIYONI SAGUNA</option>
                                        <option value="CV. ELKA MANDIRI">CV. ELKA MANDIRI</option>
                                        <option value="PT. NABA TECHNOLOGY SOLUTIONS">PT. NABA TECHNOLOGY SOLUTIONS</option>
                                        <option value="PT. DWI SRIKANDI INDONESIA">PT. DWI SRIKANDI INDONESIA</option>
                                        <option value="CV. BHIRMA TEKNIK">CV. BHIMA TEKNIK</option>
                                        <option value="PT. DWI SRIKANDI NUSANTARA">PT. DWI SRIKANDI NUSANTARA</option>`;
                    perusahaanSelect.value = perusahaan || ''; // Set nilai perusahaan

                    // Input untuk nilai paket
                    const nilaiPaketInput = document.createElement('input');
                    nilaiPaketInput.type = 'number';
                    nilaiPaketInput.name = 'nilai[]';
                    nilaiPaketInput.className = 'w-full border-gray-300 rounded p-2';
                    nilaiPaketInput.placeholder = 'Nilai Holding';
                    nilaiPaketInput.value = nilaiPaketArray[index] || ''; // Set nilai paket berdasarkan index
                    nilaiPaketInput.required = true;

                    // Tombol hapus
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'remove-perusahaan bg-red-600 text-white px-2 py-1 rounded';
                    removeButton.textContent = 'Hapus';

                    removeButton.addEventListener('click', () => {
                        newperusahaanItem.remove();
                    });

                    // Susun elemen dalam container
                    newperusahaanItem.appendChild(perusahaanSelect);
                    newperusahaanItem.appendChild(nilaiPaketInput);
                    newperusahaanItem.appendChild(removeButton);

                    perusahaanContainer.appendChild(newperusahaanItem);
                });

                // Tampilkan modal
                modal.classList.remove('hidden');
            }
            // Initial Load
            updateData();
        </script>

</body>

</html>
