<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Penjualan Perusahaan</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Rekap Penjualan Perusahaan</h1>

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
                    <div id="perusahaan-container">
                        <div class="perusahaan-item flex items-center space-x-2 mb-2">
                            <select name="perusahaan[]" class="w-full border-gray-300 rounded p-2 perusahaan-select"
                                required>
                                <option value="" disabled selected>Pilih Perusahaan</option>
                                <option value="CV. BUANA KOSA">CV. BUANA KOSA</option>
                                <option value="PT. BALI UNGGUL SEJAHTERA">PT. BALI UNGGUL SEJAHTERA</option>
                                <option value="CV. DANA RASA">CV. DANA RASA</option>
                                <option value="CV. LAGAAN SAKETI">CV. LAGAAN SAKETI</option>
                                <option value="CV. BALI JAKTI INFORMATIK">CV. BALI JAKTI INFORMATIK</option>
                                <option value="CV. BALI LINGGA KOMPUTER">CV. BALI LINGGA KOMPUTER</option>
                                <option value="CV. ARTSOLUTION">CV. ARTSOLUTION</option>
                                <option value="PT. BALI LINGGA KOMPUTER">PT. BALI LINGGA KOMPUTER</option>
                                <option value="CV. SAHABAT UTAMA">CV. SAHABAT UTAMA</option>
                                <option value="CV. N & b NET ACCESS">CV. N & b NET ACCESS</option>
                                <option value="PT. ELKA SOLUTION NUSANTARA">PT. ELKA SOLUTION NUSANTARA</option>
                                <option value="CV. ARINDAH">CV. ARINDAH</option>
                                <option value="ARFALINDO">ARFALINDO</option>
                            </select>
                            <input type="text" name="nilai[]" class="w-full border-gray-300 rounded p-2"
                                placeholder="Nilai " required>
                            <button type="button"
                                class="remove-perusahaan bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </div>
                    </div>
                    <button type="button" id="add-perusahaan" class="bg-green-500 text-white px-4 py-2 rounded">Tambah
                        Perusahaan</button>
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
                    <th class="border border-gray-300 px-4 py-2">Perusahaan</th>
                    <th class="border border-gray-300 px-4 py-2">Nilai Paket</th>
                    <th class="border border-gray-300 px-4 py-2">Keterangan</th>
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
        document.getElementById('add-perusahaan').addEventListener('click', () => {
            const perusahaanContainer = document.getElementById('perusahaan-container');

            // Membuat elemen perusahaan dan nilai 
            const newperusahaanItem = document.createElement('div');
            newperusahaanItem.className = 'perusahaan-item flex items-center space-x-2 mb-2';

            const perusahaanSelect = document.createElement('select');
            perusahaanSelect.name = 'perusahaan[]';
            perusahaanSelect.className = 'w-full border-gray-300 rounded p-2 perusahaan-select';
            perusahaanSelect.required = true;

            // Menambahkan opsi default dan perusahaan
            perusahaanSelect.innerHTML = `
                <option value="" disabled selected>Pilih perusahaan</option>
                  <option value="CV. BUANA KOSA">CV. BUANA KOSA</option>
                                    <option value="PT. BALI UNGGUL SEJAHTERA">PT. BALI UNGGUL SEJAHTERA</option>
                                    <option value="CV. DANA RASA">CV. DANA RASA</option>
                                    <option value="CV. LAGAAN SAKETI">CV. LAGAAN SAKETI</option>
                                    <option value="CV. BALI JAKTI INFORMATIK">CV. BALI JAKTI INFORMATIK</option>
                                    <option value="CV. BALI LINGGA KOMPUTER">CV. BALI LINGGA KOMPUTER</option>
                                    <option value="CV. ARTSOLUTION">CV. ARTSOLUTION</option>
                                    <option value="PT. BALI LINGGA KOMPUTER">PT. BALI LINGGA KOMPUTER</option>
                                    <option value="CV. SAHABAT UTAMA">CV. SAHABAT UTAMA</option>
                                    <option value="CV. N & b NET ACCESS">CV. N & b NET ACCESS</option>
                                    <option value="PT. ELKA SOLUTION NUSANTARA">PT. ELKA SOLUTION NUSANTARA</option>
                                    <option value="CV. ARINDAH">CV. ARINDAH</option>
                                    <option value="ARFALINDO">ARFALINDO</option>              `;

            const nilaiInput = document.createElement('input');
            nilaiInput.type = 'text';
            nilaiInput.name = 'nilai[]';
            nilaiInput.className = 'w-full border-gray-300 rounded p-2';
            nilaiInput.placeholder = 'Nilai ';
            nilaiInput.required = true;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'remove-perusahaan bg-red-500 text-white px-2 py-1 rounded';
            removeButton.textContent = 'Hapus';

            // Menambahkan logika hapus
            removeButton.addEventListener('click', () => {
                newperusahaanItem.remove();
            });

            // Menambahkan elemen ke container
            newperusahaanItem.appendChild(perusahaanSelect);
            newperusahaanItem.appendChild(nilaiInput);
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
    const response = await fetch(url, {
        method: editMode ? 'PUT' : 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        throw new Error(`HTTP Error: ${response.status}`);
    }

    const result = await response.json();
    console.log('Server Response:', result);

    if (result.success) {
        alert('Data berhasil disimpan.');
        modal.classList.add('hidden');
        updateData();
    } else {
        alert(result.message || 'Gagal menyimpan data.');
    }
} catch (error) {
    console.error('Error saat menyimpan data:', error.message);
    alert('Terjadi kesalahan: ' + error.message);
}
        }

        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const bulanTahun = document.getElementById('modal-bulan_tahun').value.trim();
            const perusahaanList = [...document.querySelectorAll('select[name="perusahaan[]"]')].map(select =>
                select.value.trim());
            const nilaiList = [...document.querySelectorAll('input[name="nilai[]"]')].map(
                input => parseFloat(input.value.trim()));

            if (!bulanTahun || perusahaanList.some(p => !p.trim()) || nilaiList.some(n => isNaN(n))) {
                alert('Pastikan semua kolom diisi dengan benar!');
                return;
            }


            const payload = {
                id: editId,
                bulan_tahun: bulanTahun,
                perusahaan: perusahaanList,
                nilai: nilaiList
            };

            const url = editMode ? `/marketings/rekappenjualanperusahaans/update/${editId}` :
                '/marketings/rekappenjualanperusahaans/store';

            // // const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            // // if (!csrfToken) {
            // //     alert('CSRF Token tidak ditemukan!');
            // //     return;
                
            // }
            console.log('Payload:', payload);
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
                console.log('Response:', result);
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
                const response = await fetch(`/marketings/rekappenjualanperusahaans/destroy/${id}`, {
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
            const url = filter ? `/marketings/rekappenjualanperusahaans/data?bulan_tahun=${filter}` :
                '/marketings/rekappenjualanperusahaans/data';

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
                    <td class="border px-4 py-2">${item.perusahaan}</td>
                    <td class="border px-4 py-2">Rp ${item.nilai.toLocaleString()}</td>
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
            parsedData.perusahaan = Array.isArray(parsedData.perusahaan) ?
                parsedData.perusahaan :
                (typeof parsedData.perusahaan === 'string' ? parsedData.perusahaan.split(',') : []);

            // Pastikan `nilai` adalah array
            parsedData.nilai = Array.isArray(parsedData.nilai) ?
                parsedData.nilai :
                (typeof parsedData.nilai === 'string' ? parsedData.nilai.split(',').map(Number) : []);

            editMode = true; // Aktifkan mode edit
            editId = id; // Simpan ID data yang sedang diedit

            // Set judul modal
            modalTitle.textContent = 'Edit Data';
            document.getElementById('modal-bulan_tahun').value = parsedData.bulan_tahun;

            // Bersihkan container perusahaan
            const perusahaanContainer = document.getElementById('perusahaan-container');
            perusahaanContainer.innerHTML = '';

            // Tambahkan elemen perusahaan dan nilai pendapatan
            parsedData.perusahaan.forEach((perusahaan, index) => {
                const newperusahaanItem = document.createElement('div');
                newperusahaanItem.className = 'perusahaan-item flex items-center space-x-2 mb-2';

                const perusahaanSelect = document.createElement('select');
                perusahaanSelect.name = 'perusahaan[]';
                perusahaanSelect.className = 'w-full border-gray-300 rounded p-2 perusahaan-select';
                perusahaanSelect.required = true;

                perusahaanSelect.innerHTML = `
            <option value="" disabled>Pilih Perusahaan</option>
       <option value="CV. BUANA KOSA">CV. BUANA KOSA</option>
                                    <option value="PT. BALI UNGGUL SEJAHTERA">PT. BALI UNGGUL SEJAHTERA</option>
                                    <option value="CV. DANA RASA">CV. DANA RASA</option>
                                    <option value="CV. LAGAAN SAKETI">CV. LAGAAN SAKETI</option>
                                    <option value="CV. BALI JAKTI INFORMATIK">CV. BALI JAKTI INFORMATIK</option>
                                    <option value="CV. BALI LINGGA KOMPUTER">CV. BALI LINGGA KOMPUTER</option>
                                    <option value="CV. ARTSOLUTION">CV. ARTSOLUTION</option>
                                    <option value="PT. BALI LINGGA KOMPUTER">PT. BALI LINGGA KOMPUTER</option>
                                    <option value="CV. SAHABAT UTAMA">CV. SAHABAT UTAMA</option>
                                    <option value="CV. N & b NET ACCESS">CV. N & b NET ACCESS</option>
                                    <option value="PT. ELKA SOLUTION NUSANTARA">PT. ELKA SOLUTION NUSANTARA</option>
                                    <option value="CV. ARINDAH">CV. ARINDAH</option>
                                    <option value="ARFALINDO">ARFALINDO</option>          `;
                perusahaanSelect.value = perusahaan; // Set nilai perusahaan

                const nilaiInput = document.createElement('input');
                nilaiInput.type = 'text';
                nilaiInput.name = 'nilai[]';
                nilaiInput.className = 'w-full border-gray-300 rounded p-2';
                nilaiInput.placeholder = 'Nilai Paket';
                nilaiInput.value = parsedData.nilai[index] || ''; // Set nilai pendapatan
                nilaiInput.required = true;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'remove-perusahaan bg-red-500 text-white px-2 py-1 rounded';
                removeButton.textContent = 'Hapus';

                removeButton.addEventListener('click', () => {
                    newperusahaanItem.remove();
                });

                newperusahaanItem.appendChild(perusahaanSelect);
                newperusahaanItem.appendChild(nilaiInput);
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
