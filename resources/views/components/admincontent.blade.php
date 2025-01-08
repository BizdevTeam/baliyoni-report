<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Container untuk Laporan -->
    <div class="flex flex-wrap justify-between gap-4 mt-6">
        <!-- Laporan Paket Administrasi -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Laporan Paket Administrasi</h1>
            <canvas id="chartPaketAdministrasi" class="w-full h-64"></canvas>
        </div>

        <!-- Laporan Stok -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Laporan Stok</h1>
            <canvas id="chartStok" class="w-full h-64"></canvas>
        </div>
    </div>

    <div class="flex flex-wrap justify-between gap-4 mt-6">
        <!-- Pie Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Kas, Hutang, Piutang, Stok</h1>
            <div class="flex justify-center items-center w-full h-[250px]">
                <canvas id="chartPie1" class=" w-full h-[250px]"></canvas>
            </div>
        </div>

        <!-- Laporan Sakit -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Laporan Sakit</h1>
            <canvas id="chartSakit" class="w-full h-64"></canvas>
        </div>
    </div>
    <div class="flex flex-wrap justify-between gap-4 mt-6">
        <!-- Pie Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Arus Kas</h1>
            <div class="flex justify-center items-center  w-full h-[250px]">
                <canvas id="chartPie2" class=" w-full h-[250px]"></canvas>
            </div>
        </div>

        <!-- Laporan Sakit -->
        <div class="bg-white rounded-lg shadow-lg p-6 flex-1 md:max-w-[49%]">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">Rekap Penjualan Perusahaan</h1>
            <canvas id="chartPenjualanPerusahaan" class="w-full h-64"></canvas>
        </div>
    </div>
</div>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Utility function to fetch chart data
    async function fetchChartData(url) {
        try {
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                return result.data;
            } else {
                console.error('Failed to fetch data:', result.message);
                alert('Gagal memuat data: ' + result.message);
                return null;
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            alert('Terjadi kesalahan saat memuat data.');
            return null;
        }
    }

    // Utility function to render chart
    function renderBarChart(ctxId, labels, data, title, datasetLabel) {
        const ctx = document.getElementById(ctxId).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: data,
                    backgroundColor: data.map(() =>
                        `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
                    ),
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Rp ${context.raw.toLocaleString()}`; // Format tooltip angka
                            },
                        },
                    },
                    title: {
                        display: true,
                        text: title,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return `Rp ${value.toLocaleString()}`;
                            },
                        },
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0,
                        },
                    },
                },
            },
        });
    }

    function renderBarChart2(ctxId, labels, data, title, datasetLabel) {
        const ctx = document.getElementById(ctxId).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: data,
                    backgroundColor: data.map(() =>
                        `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
                    ),
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Total : ${context.raw.toLocaleString()} Kali`; // Format tooltip angka
                            },
                        },
                    },
                    title: {
                        display: true,
                        text: title,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0,
                        },
                    },
                },
            },
        });
    }

    function renderPieChart(canvasId, labels, data, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#2ab952'],
                hoverBackgroundColor: ['#FF4757', '#3B8BEB', '#FFD700', '#00a623']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return labels[tooltipItem.dataIndex] + ': ' + data[tooltipItem.dataIndex].toLocaleString();
                        }
                    }
                }
            },
        }
    });
}

    (async function loadCharts() {
        try {
            // Paket Administrasi
            const paketData = await fetchChartData(
                '{{ route('marketings.laporanpaketadministrasi.chartdata') }}');
            if (paketData) {
                const limitedPaketData = paketData.slice(0, 5); // Ambil hanya 12 data pertama
                const labels = limitedPaketData.map(item => `${item.website} (${item.bulan_tahun})`);
                const values = limitedPaketData.map(item => Number(item.total_rp));
                renderBarChart(
                    'chartPaketAdministrasi',
                    labels,
                    values,
                    'Grafik Laporan Paket Administrasi',
                    'Total Paket Administrasi (Rp)'
                );
            }

            // Stok
            const stokData = await fetchChartData('{{ route('laporanstok.index') }}');
            if (stokData) {
                const labels = stokData.map(item => item.bulan_tahun);
                const values = stokData.map(item => Number(item.stok));
                renderBarChart('chartStok', labels, values, 'Grafik Laporan Stok', 'Total Stok');
            }

            // Kas, Hutang, Piutang, Stok
            const kashutangpiutangstokData = await fetchChartData(
                '{{ route('khps.index') }}');
            if (kashutangpiutangstokData) {
                // Hitung total untuk setiap kategori
                const totalData = {
                    kas: kashutangpiutangstokData.reduce((sum, item) => sum + item.kas, 0),
                    hutang: kashutangpiutangstokData.reduce((sum, item) => sum + item.hutang, 0),
                    piutang: kashutangpiutangstokData.reduce((sum, item) => sum + item.piutang, 0),
                    stok: kashutangpiutangstokData.reduce((sum, item) => sum + item.stok, 0),
                };

                // Render chart dengan data yang diperoleh
                renderPieChart(
                    'chartPie1',
                    ['Kas', 'Hutang', 'Piutang', 'Stok'],
                    [totalData.kas, totalData.hutang, totalData.piutang, totalData.stok],
                    'Distribusi Kas, Hutang, Piutang, dan Stok'
                );
            }
            const aruskasData = await fetchChartData('{{ route('aruskas.index') }}');
            if (aruskasData) {
                const totalData = {
                    kas_masuk: aruskasData.reduce((sum, item) => sum + item.kas_masuk, 0),
                    kas_keluar: aruskasData.reduce((sum, item) => sum + item.kas_keluar, 0),

                };

                renderPieChart(
                    'chartPie2',
                    ['Kas Masuk', 'Kas Keluar'],
                    [totalData.kas_masuk, totalData.kas_keluar],
                    'Arus Kas'
                );
            }

            // Laporan Sakit
            const sakitData = await fetchChartData('{{ route('hrga.laporansakit.data') }}');
            if (sakitData) {
                const limitedSakitData = sakitData.slice(0, 5); 
                const labels = limitedSakitData.map(item => item.nama);
                const values = limitedSakitData.map(item => Number(item.total_sakit));
                renderBarChart2('chartSakit', labels, values, 'Grafik Laporan Sakit', 'Total Sakit');
            }

            const ppData = await fetchChartData('{{ route('marketings.rekappenjualanperusahaan.data') }}');
            if (ppData) {
                const limitedPpData = ppData.slice(0, 5); // Ambil hanya 12 data pertama
                const labels = limitedPpData.map(item => `${item.perusahaan} (${item.bulan_tahun})`);
                const values = limitedPpData.map(item => Number(item.nilai_paket));
                renderBarChart('chartPenjualanPerusahaan', labels, values, 'Grafik Rekap Penjualan Perusahaan',
                    'Rekap Penjualan Perusahaan');
            }

        } catch (error) {
            console.error('Error loading charts:', error);
        }
    })();
</script>
