<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <p class="text-lg font-bold text-gray-800">WEB REPORT FOR BALIYONI</p>

    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl mt-4">
        <h1 class="text-2xl font-semibold mb-4 text-gray-700">Laporan Paket Administrasi</h1>
        <canvas id="myChart1" class="w-full"></canvas>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl mt-4">
        <h1 class="text-2xl font-semibold mb-4 text-gray-700">Laporan Paket Administrasi</h1>
        <canvas id="myChart1" class="w-full"></canvas>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Fetch data and create chart
    async function fetchChartData() {
        try {
            const response = await fetch('{{ route("marketings.laporanpaketadministrasi.chartdata") }}'); // Route Laravel
            const result = await response.json();

            if (result.success) {
                renderChart(result.data);
            } else {
                alert('Gagal memuat data: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data.');
        }
    }

    function renderChart(data) {
        // Mapping data untuk label dengan bulan dan tahun
        const labels = data.map(item => `${item.website} (${item.bulan_tahun})`);
        const values = data.map(item => Number(item.total_rp));

        // Generate warna acak untuk setiap bar
        const backgroundColors = data.map(() =>
            `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
        );

        // Buat chart menggunakan Chart.js
        const ctx = document.getElementById('myChart1').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Paket Administrasi (Rp)',
                    data: values,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Rp ${context.raw.toLocaleString()}`; // Format tooltip angka
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Grafik Laporan Paket Administrasi',
                        font: { size: 16, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return `Rp ${value.toLocaleString()}`; // Format angka pada sumbu Y
                            }
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false, // Pastikan label tidak dipotong
                            maxRotation: 45, // Rotasi jika label panjang
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }

    // Panggil fungsi fetch data chart
    fetchChartData();
</script>
