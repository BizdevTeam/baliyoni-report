<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Paket Administrasi</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Laporan Paket Administrasi</h1>

        <!-- Form -->
        <form id="paket-form" class="space-y-4 mb-8">
            @csrf <!-- Menambahkan token CSRF -->
            <div>
                <label for="bulan_tahun" class="block text-sm font-medium">Bulan/Tahun</label>
                <input type="text" id="bulan_tahun" name="bulan_tahun" class="w-full border-gray-300 rounded p-2"
                    placeholder="mm/yyyy" required>
            </div>
            <div>
                <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" class="w-full border-gray-300 rounded p-2"
                    placeholder="Keterangan (opsional)">
            </div>
            <div>
                <label for="website" class="block text-sm font-medium">Website</label>
                <select id="website" name="website" class="w-full border-gray-300 rounded p-2" required>
                    <option value="" disabled selected>Pilih Website</option>
                    <option value="E - Katalog">E - Katalog</option>
                    <option value="E - Katalog Luar Bali">E - Katalog Luar Bali</option>
                    <option value="Balimall">Balimall</option>
                    <option value="Siplah">Siplah</option>
                </select>
            </div>
            <div>
                <label for="paket_rp" class="block text-sm font-medium">Paket (RP)</label>
                <input type="text" id="paket_rp" name="paket_rp" class="w-full border-gray-300 rounded p-2"
                    placeholder="0" min="0" required>
            </div>
            <button type="button" id="add-paket" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Paket</button>
        </form>

        <div id="feedback" class="text-sm text-red-500 hidden"></div>

        <div class="mb-4">
            <label for="filter-bulan-tahun" class="block text-sm font-medium">Filter Bulan/Tahun</label>
            <input type="text" id="filter-bulan-tahun" class="border-gray-300 rounded p-2" placeholder="mm/yyyy">
            <button type="button" id="apply-filter" class="bg-green-500 text-white px-4 py-2 rounded">Terapkan
                Filter</button>
        </div>

        <!-- Chart -->
        <div class="mt-6">
            <canvas id="chart"></canvas>
        </div>

        <!-- Table -->
        <div class="mt-8">
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Bulan/Tahun</th>
                        <th class="border border-gray-300 px-4 py-2">Website</th>
                        <th class="border border-gray-300 px-4 py-2">Paket (RP)</th>
                        <th class="border border-gray-300 px-4 py-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="data-table"></tbody>
            </table>
        </div>
    </div>

    <script>
        const form = document.getElementById('paket-form');
        const feedback = document.getElementById('feedback');
        const chartCanvas = document.getElementById('chart');
        let usedWebsites = {}; // Menyimpan website yang sudah dipakai per bulan/tahun

        document.getElementById('add-paket').addEventListener('click', () => {
            feedback.textContent = '';
            feedback.classList.add('hidden');

            if (!form.reportValidity()) return;

            const bulanTahun = document.getElementById('bulan_tahun').value;
            const website = document.getElementById('website').value;

            // Cek apakah website sudah dipakai dalam bulan/tahun yang sama
            if (usedWebsites[bulanTahun] && usedWebsites[bulanTahun].includes(website)) {
                feedback.textContent = `Website ${website} sudah dipilih untuk bulan ${bulanTahun}.`;
                feedback.classList.remove('hidden');
                return;
            }

            // Simpan data sementara
            if (!usedWebsites[bulanTahun]) {
                usedWebsites[bulanTahun] = [];
            }
            usedWebsites[bulanTahun].push(website);

            const keterangan = document.getElementById('keterangan').value;
            const paketRp = document.getElementById('paket_rp').value;

            const data = {
                bulan_tahun: bulanTahun,
                keterangan: keterangan,
                website: website,
                paket_rp: Number(paketRp),
            };

            fetch('/marketings/laporanpaketadministrasi/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(responseData => {
                    if (responseData.success) {
                        updateChart();
                        updateTable();
                    } else {
                        feedback.textContent = responseData.message || 'Gagal menyimpan data.';
                        feedback.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    feedback.textContent = error.message || 'Terjadi kesalahan saat mengirim data.';
                    feedback.classList.remove('hidden');
                });
        });


        function updateChart(filterBulanTahun = '') {
            fetch(`/marketings/laporanpaketadministrasi/data?bulan_tahun=${filterBulanTahun}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) return;

                    const labels = data.data.map(item => item.website);
                    const values = data.data.map(item => item.paket_rp);

                    if (chart) chart.destroy();

                    chart = new Chart(chartCanvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Paket (RP)',
                                data: values,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderWidth: 1,
                            }],
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                },
                            },
                        },
                    });
                });
        }

        function updateTable(filterBulanTahun = '') {
            fetch(`/marketings/laporanpaketadministrasi/data?bulan_tahun=${filterBulanTahun}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) return;

                    const tableBody = document.getElementById('data-table');
                    tableBody.innerHTML = '';

                    data.data.forEach(item => {
                        const row = `
                    <tr>
                        <td class="border px-4 py-2">${item.bulan_tahun}</td>
                        <td class="border px-4 py-2">${item.website}</td>
                        <td class="border px-4 py-2">${item.paket_rp}</td>
                        <td class="border px-4 py-2">${item.keterangan || '-'}</td>
                    </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                });
        }

        function syncUsedWebsites() {
            fetch('/marketings/laporanpaketadministrasi/data')
                .then(response => response.json())
                .then(data => {
                    if (!data.success) return;

                    usedWebsites = {}; // Reset
                    data.data.forEach(item => {
                        if (!usedWebsites[item.bulan_tahun]) {
                            usedWebsites[item.bulan_tahun] = [];
                        }
                        usedWebsites[item.bulan_tahun].push(item.website);
                    });
                });
        }
        document.getElementById('apply-filter').addEventListener('click', () => {
            const filterBulanTahun = document.getElementById('filter-bulan-tahun').value;

            const bulanTahunRegex = /^(0[1-9]|1[0-2])\/\d{4}$/;
            if (!bulanTahunRegex.test(filterBulanTahun)) {
                feedback.textContent = 'Format Bulan/Tahun harus mm/yyyy.';
                feedback.classList.remove('hidden');
                return;
            }

            feedback.classList.add('hidden');
            updateChart(filterBulanTahun);
            updateTable(filterBulanTahun);
            let chart;
        });
    </script>
</body>

</html>
