<!-- Floating Button untuk Search -->
<div class="fixed bottom-6 right-6 z-50">
  <button id="floatingSearchButton" class="justify-center rounded-full w-20 h-20 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-4 py-3 shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
      <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <path stroke-dasharray="40" stroke-dashoffset="40" d="M10.76 13.24c-2.34 -2.34 -2.34 -6.14 0 -8.49c2.34 -2.34 6.14 -2.34 8.49 0c2.34 2.34 2.34 6.14 0 8.49c-2.34 2.34 -6.14 2.34 -8.49 0Z">
          <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="40;0"/>
        </path>
        <path stroke-dasharray="12" stroke-dashoffset="12" d="M10.5 13.5l-7.5 7.5">
          <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s" values="12;0"/>
        </path>
      </g>
    </svg>
  </button>
</div>

<!-- Search Modal -->
<div id="searchModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-6 w-96 relative">
    <!-- Tombol Close -->
    <button id="closeSearchModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">&times;</button>

    <!-- Search Form -->
    <form id="chartFilterForm" method="GET" action="#" class="mb-4">
      <div class="flex items-center gap-2">
        <input type="text" id="searchInput" name="search" placeholder="Search by Month" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
        <button type="submit" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>
  </div>
</div>  
  
  <!-- Script untuk Floating Search Button dan Modal -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const floatingButton = document.getElementById('floatingSearchButton');
    const searchModal = document.getElementById('searchModal');
    const closeSearchModal = document.getElementById('closeSearchModal');
    const searchForm = document.getElementById('chartFilterForm');
    const searchInput = document.getElementById('searchInput');

    // Tampilkan modal saat tombol pencarian diklik
    floatingButton.addEventListener('click', function (event) {
        event.stopPropagation();
        searchModal.classList.remove('hidden');
    });

    // Sembunyikan modal saat tombol close diklik
    closeSearchModal.addEventListener('click', function () {
        searchModal.classList.add('hidden');
    });

    // Sembunyikan modal saat klik di luar modal
    document.addEventListener('click', function (event) {
        if (!searchModal.contains(event.target) && !floatingButton.contains(event.target)) {
            searchModal.classList.add('hidden');
        }
    });

    // Fungsi menangani pencarian data tanpa reload halaman
    searchForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Hindari reload halaman

        let searchValue = searchInput.value.trim();
        let queryString = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';

        // Update grafik dengan filter pencarian
        updateCharts(queryString);

        // Tutup modal setelah pencarian
        searchModal.classList.add('hidden');
    });
});

// Fungsi untuk memperbarui grafik berdasarkan pencarian
function updateCharts(queryString) {
    const chartRoutes = {
        'chartp': 'adminpenjualan.chart.data',
        'chartpp': 'adminpp.chart.data',
        'chartl': 'admin.chart.data',
        'chartsp': 'adminstatuspaket.chart.data',
        'chartpi': 'adminperinstansi.chart.data',
        'chartph': 'adminholding.chart.data',
        'chartls': 'adminstok.chart.data',
        'chartpo': 'adminoutlet.chart.data',
        'chartln': 'adminnegosiasi.chart.data',
        'chartlrp': 'adminpendapatanservisasp.chart.data',
        'chartlrps': 'adminpiutangservisasp.chart.data',
        'chartrpdb': 'adminpendapatanpengirimanbali.chart.data',
        'chartrplb': 'adminpendapatanpengirimanluarbali.chart.data',
        'charts': 'adminsakit.chart.data',
        'chartizin': 'adminizin.chart.data',
        'chartcuti': 'admincuti.chart.data',
        'chartterlambat': 'adminterlambat.chart.data'
    };

    for (let chartId in chartRoutes) {
        let route = chartRoutes[chartId];
        fetchChartData(`/${route}${queryString}`, chartId);
    }
}

function fetchChartData(url, canvasId) {
    fetch(url)
        .then(response => {
            if(!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(chartData => {
            let chartCanvas = document.getElementById(canvasId);
            // Hancurkan chart sebelumnya jika ada
            if(chartCanvas.chart) {
                chartCanvas.chart.destroy();
            }
            // Buat chart baru
            chartCanvas.chart = new Chart(
                chartCanvas.getContext('2d'),
                {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets,
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Data'
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                }
            );
        })
        .catch(error => console.error('Error:', error));
}

</script>