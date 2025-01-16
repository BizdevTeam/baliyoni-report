{{-- <div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Container untuk Laporan -->
    
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Grafik Laporan Paket Administrasi</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartCanvas" class="w-full h-96"></canvas>
        </div>
    </div>

</div>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil data chart dari backend
        fetch("{{ route('laporanpaketadministrasi.getChartData') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const chartData = data.data;

                    // Konfigurasi chart menggunakan Chart.js
                    const ctx = document.getElementById('chartCanvas').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar', // Ganti ke 'line', 'pie', atau lainnya jika diperlukan
                        data: {
                            labels: chartData.labels,
                            datasets: chartData.datasets
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: 'Grafik Laporan Paket Administrasi'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Website'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Total Paket'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    console.error('Error fetching chart data:', data.message);
                    alert('Gagal memuat data chart.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data chart.');
            });
    });
</script> --}}
