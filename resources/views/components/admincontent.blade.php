<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
      <P>WEB REPORT FOR BALIYONI</P>
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl mt-4">
        <h1 class="text-2xl font-semibold mb-4 text-gray-700">Laporan Paket Administrasi<span class="">Next</span></h1>
        <canvas id="myChart" class="w-full"></canvas>
    </div>
</div>

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
        const labels = data.map(item => item.website);
        const values = data.map(item => Number(item.paket_rp));
        const backgroundColors = data.map(() =>
            `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
        );

        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Paket Administrasi (Rp)',
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
                                return `Rp ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return `Rp ${value.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Panggil fungsi fetch data chart
    fetchChartData();
</script>
</body>
</html>
