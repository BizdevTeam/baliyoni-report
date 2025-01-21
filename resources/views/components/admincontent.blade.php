<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Grafik Laporan Paket Administrasi -->
    <div class="p-4">
        <h1 class="text-4xl font-bold text-gray-800">Dash<span class="text-red-600">board</span></h1>
        <div class="flex justify-end mb-4">

            <form id="chartFilterForm" method="GET" action="#" class="flex items-center gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input 
                        type="month" 
                        id="searchInput"
                        name="search" 
                        placeholder="Search by Month" 
                        value="{{ request('search') }}" 
                        class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" 
                    />
                </div>
                <button type="submit" class="bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" aria-label="Search">
                    Search
                </button>
            </form>          
              
        </div>
    </div>
    <div class="gap-2 grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2">
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform hover:scale-90 hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Grafik Laporan Rekap Penjualan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartp" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappenjualan.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Penjualan →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform hover:scale-90 hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Grafik Laporan Rekap Penjualan Perusahaan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartpp" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappenjualanperusahaan.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Penjualan Perusahaan →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform hover:scale-90 hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Grafik Laporan Paket Administrasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartl" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanpaketadministrasi.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Paket Administrasi →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform hover:scale-90 hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Grafik Laporan Sakit</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="charts" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Sakit →</a>
        </div>
    </div>
</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk memuat data awal grafik saat halaman pertama kali dibuka
    function loadInitialChartData() {
        fetchChartData('{{ route("adminpenjualan.chart.data") }}', 'chartp');
        fetchChartData('{{ route("adminpp.chart.data") }}', 'chartpp');
        fetchChartData('{{ route("admin.chart.data") }}', 'chartl');
        fetchChartData('{{ route("adminsakit.chart.data") }}', 'charts');
    }

    // Memanggil fungsi loadInitialChartData saat halaman dimuat
    document.addEventListener("DOMContentLoaded", loadInitialChartData);

    document.getElementById('chartFilterForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Mencegah reload halaman

    let searchValue = document.getElementById('searchInput').value;
    let queryString = '';

    if (searchValue) {
        queryString += `?search=${searchValue}`;
    }

    fetchChartData('{{ route("adminpenjualan.chart.data") }}' + queryString, 'chartp');
    fetchChartData('{{ route("adminpp.chart.data") }}' + queryString, 'chartpp');
    fetchChartData('{{ route("admin.chart.data") }}' + queryString, 'chartl');
    fetchChartData('{{ route("adminsakit.chart.data") }}' + queryString, 'charts');
});


    function fetchChartData(url, canvasId) {
        fetch(url)
            .then(response => response.json())
            .then(chartData => {
                let chartCanvas = document.getElementById(canvasId);
                if (chartCanvas.chart) {
                    chartCanvas.chart.destroy();
                }
                chartCanvas.chart = new Chart(chartCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets,
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
                        },
                        scales: {
                            x: { title: { display: true, text: 'Bulan' }},
                            y: { beginAtZero: true },
                        },
                    },
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }
</script>

