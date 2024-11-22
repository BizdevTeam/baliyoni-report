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
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Laporan Paket Administrasi</h1>

        <form id="paket-form" class="space-y-4">
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
                    <option value="Website 1">Website 1</option>
                    <option value="Website 2">Website 2</option>
                </select>
            </div>
            <div>
                <label for="paket_rp" class="block text-sm font-medium">Paket (RP)</label>
                <input type="text" id="paket_rp" name="paket_rp" class="w-full border-gray-300 rounded p-2"
                    placeholder="0" min="0" required>
            </div>
            <button type="button" id="add-paket" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Paket</button>
        </form>

        <div id="feedback" class="text-sm text-red-500 mt-2 hidden"></div>

        <div class="mt-6">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <script>
        const form = document.getElementById('paket-form');
        const feedback = document.getElementById('feedback');
        const chartCanvas = document.getElementById('chart');
        let chart;
    
        document.getElementById('add-paket').addEventListener('click', () => {
            // Reset feedback
            feedback.textContent = '';
            feedback.classList.add('hidden');
    
            // Validasi input sebelum dikirim
            if (!form.reportValidity()) return;
    
            // Ambil nilai input dari form
            const bulanTahun = document.getElementById('bulan_tahun').value;
            const keterangan = document.getElementById('keterangan').value;
            const website = document.getElementById('website').value;
            const paketRp = document.getElementById('paket_rp').value;
    
            // Validasi format Bulan/Tahun
            const bulanTahunRegex = /^(0[1-9]|1[0-2])\/\d{4}$/;
            if (!bulanTahunRegex.test(bulanTahun)) {
                feedback.textContent = 'Format Bulan/Tahun harus mm/yyyy.';
                feedback.classList.remove('hidden');
                return;
            }
    
            // Validasi paket_rp sebagai angka positif
            const paketRpValue = Number(paketRp);
            if (isNaN(paketRpValue) || paketRpValue < 0) {
                feedback.textContent = 'Paket (RP) harus berupa angka positif.';
                feedback.classList.remove('hidden');
                return;
            }
    
            // Siapkan data untuk dikirim ke server
            const data = {
                bulan_tahun: bulanTahun,
                keterangan: keterangan,
                website: website,
                paket_rp: paketRpValue,
            };
    
            console.log('Data yang dikirim:', data); // Log untuk debugging
    
            // Kirim data ke server menggunakan fetch
            fetch('/marketings/laporanpaketadministrasi/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                console.log('Response Status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(responseData => {
                if (responseData.success) {
                    console.log('Data berhasil disimpan:', responseData);
                    updateChart(); // Perbarui chart setelah data disimpan
                } else {
                    console.error('Data gagal disimpan:', responseData);
                    feedback.textContent = responseData.message || 'Gagal menyimpan data.';
                    feedback.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Kesalahan jaringan atau lainnya:', error);
                feedback.textContent = 'Terjadi kesalahan saat mengirim data.';
                feedback.classList.remove('hidden');
            });
        });
    
        function updateChart() {
            fetch('/marketings/laporanpaketadministrasi/data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data untuk chart:', data);
    
                    // Periksa apakah respons berisi array data
                    if (!data.success || !Array.isArray(data.data)) {
                        console.error('Data yang diterima tidak valid:', data);
                        feedback.textContent = data.message || 'Data tidak valid untuk chart.';
                        feedback.classList.remove('hidden');
                        return;
                    }
    
                    const labels = data.data.map(item => item.website);
                    const values = data.data.map(item => item.paket_rp);
    
                    // Hancurkan chart lama jika ada
                    if (chart) chart.destroy();
    
                    // Buat chart baru
                    chart = new Chart(chartCanvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Paket (RP)',
                                data: values,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true,
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                },
                            },
                        },
                    });
                })
                .catch(error => {
                    console.error('Kesalahan saat memuat data chart:', error);
                    feedback.textContent = 'Terjadi kesalahan saat memuat data chart.';
                    feedback.classList.remove('hidden');
                });
        }
    
        // Perbarui chart saat halaman pertama kali dimuat
        updateChart();
    </script>
</body>

</html>
