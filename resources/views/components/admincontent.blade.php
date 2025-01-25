<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Grafik Laporan Paket Administrasi -->
    <div class="p-4">
        <h1 class="text-4xl font-bold text-red-600">Dash<span class="text-red-600">board</span></h1>
        <div class="flex justify-end mb-4">

            <form id="chartFilterForm" method="GET" action="#" class="flex items-center gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input 
                        type="text" 
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
    <!-- LAPORAN MARKETING -->
    <div class="gap-2 grid grid-cols-1">
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Penjualan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartp" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappenjualan.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Penjualan →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Penjualan Perusahaan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartpp" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappenjualanperusahaan.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Penjualan Perusahaan →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Paket Administrasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartl" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanpaketadministrasi.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Paket Administrasi →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Status Paket</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartsp" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("statuspaket.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Status Paket →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Per Instansi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartpi" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanperinstansi.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Per Instansi →</a>
        </div>
    </div>

<!-- LAPORAN PROCUREMENTS -->
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Pembelian (HOLDING)</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartph" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanholding.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Pembelian (HOLDING) →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Stok</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartls" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Stok →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Pembelian Outlet</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartpo" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Pembelian Outlet →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Negosiasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartln" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Negosiasi →</a>
        </div>
    </div>

<!-- LAPORAN SUPPORTS -->
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartlrp" class="mx-auto h-[600px] w-[600px]"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappendapatanservisasp.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Servis ASP →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Piutang Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartlrps" class="mx-auto h-[600px] w-[600px]"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("rekappiutangservisasp.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Piutang Servis ASP →</a>
        </div>
    </div>
    
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Pengiriman Daerah Bali</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartrpdb" class="mx-auto w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansamitra.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman Daerah Bali →</a>
        </div>
    </div>

    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Pengiriman Luar Bali</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartrplb" class="mx-auto w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporandetrans.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman Luar Bali →</a>
        </div>
    </div>

    <!-- LAPORAN HRGA -->
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Sakit</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="charts" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Sakit →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Izin</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartizin" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanizin.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Izin →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Cuti</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="charcuti" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporancuti.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Cuti →</a>
        </div>
    </div>
    <div class="p-6 border rounded-lg border-transparent shadow-lg transition-transform transform  hover:border-red-600">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Terlambat</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
            <canvas id="chartterlambat" class="w-full h-96"></canvas>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route("laporanterlambat.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Terlambat →</a>
        </div>
    </div>

</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk memuat data awal grafik saat halaman pertama kali dibuka
    function loadInitialChartData() {
        //laporan MARKETING
        fetchChartData('{{ route("adminpenjualan.chart.data") }}', 'chartp', 'Bulan ');
        fetchChartData('{{ route("adminpp.chart.data") }}', 'chartpp', 'Perusahaan ');
        fetchChartData('{{ route("admin.chart.data") }}', 'chartl', 'Nilai Paket ');
        fetchChartData('{{ route("adminstatuspaket.chart.data") }}', 'chartsp', 'Nilai Paket ');
        fetchChartData('{{ route("adminperinstansi.chart.data") }}', 'chartpi', 'Nilai Paket ');

        //laporan PROCUREMENTS
        fetchChartData('{{ route("adminholding.chart.data") }}', 'chartph', 'Perusahaan');
        fetchChartData('{{ route("adminstok.chart.data") }}', 'chartls', 'Nilai Stok ');
        fetchChartData('{{ route("adminoutlet.chart.data") }}', 'chartpo', 'Nilai Pembelian ');
        fetchChartData('{{ route("adminnegosiasi.chart.data") }}', 'chartln', 'Nilai Negosiasi ');

        //laporan SUPPORTS
        fetchChartPieData('{{ route("adminpendapatanservisasp.chart.data") }}', 'chartlrp', 'Nilai Pendapatan ');
        fetchChartPieData('{{ route("adminpiutangservisasp.chart.data") }}', 'chartlrps', 'Nilai Piutang ');
        fetchChartData('{{ route("adminpendapatanpengirimanbali.chart.data") }}', 'chartrpdb', 'Nilai Pendapatan ');
        fetchChartData('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}', 'chartrplb', 'Nilai Pendapatan ');

        //laporan HRGA
        fetchChartData('{{ route("adminsakit.chart.data") }}', 'charts', 'Nama Karyawan');
        fetchChartData('{{ route("adminizin.chart.data") }}', 'chartizin', 'Nama Karyawan');
        fetchChartData('{{ route("admincuti.chart.data") }}', 'chartcuti', 'Nama Karyawan');
        fetchChartData('{{ route("adminterlambat.chart.data") }}', 'chartterlambat', 'Nama Karyawan');

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
    //laporan MARKETING
    fetchChartData('{{ route("adminpenjualan.chart.data") }}' + queryString, 'chartp');
    fetchChartData('{{ route("adminpp.chart.data") }}' + queryString, 'chartpp');
    fetchChartData('{{ route("admin.chart.data") }}' + queryString, 'chartl');
    fetchChartData('{{ route("adminstatuspaket.chart.data") }}' + queryString, 'chartsp');
    fetchChartData('{{ route("adminperinstansi.chart.data") }}' + queryString, 'chartpi');
    
    //laporan PROCUREMENTS
    fetchChartData('{{ route("adminholding.chart.data") }}' + queryString, 'chartph');
    fetchChartData('{{ route("adminstok.chart.data") }}' + queryString, 'chartls');
    fetchChartData('{{ route("adminoutlet.chart.data") }}' + queryString, 'chartpo');
    fetchChartData('{{ route("adminnegosiasi.chart.data") }}' + queryString, 'chartln');
    
    //laporan SUPPORTS
    fetchChartPieData('{{ route("adminpendapatanservisasp.chart.data") }}' + queryString, 'chartlrp');
    fetchChartPieData('{{ route("adminpiutangservisasp.chart.data") }}' + queryString, 'chartlrps');
    fetchChartData('{{ route("adminpendapatanpengirimanbali.chart.data") }}' + queryString, 'chartrpdb');
    fetchChartData('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}' + queryString, 'chartrplb');

    //laporan HRGA
    fetchChartData('{{ route("adminsakit.chart.data") }}' + queryString, 'charts');
    fetchChartData('{{ route("adminizin.chart.data") }}' + queryString, 'chartizin');
    fetchChartData('{{ route("admincuti.chart.data") }}' + queryString, 'chartcuti');
    fetchChartData('{{ route("adminterlambat.chart.data") }}' + queryString, 'chartterlambat');
});


    function fetchChartData(url, canvasId, title) {
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
                            legend: { display: false },
                        },
                        scales: {
                            x: { title: { display: true, text : title }},
                            y: { beginAtZero: true },
                        },
                    },
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }
    
    function fetchChartPieData(url, canvasId, title) {
    fetch(url)
        .then(response => response.json())
        .then(chartData => {
            let chartCanvas = document.getElementById(canvasId);
            if (chartCanvas.chart) {
                chartCanvas.chart.destroy(); // Hapus chart sebelumnya jika ada
            }
            chartCanvas.chart = new Chart(chartCanvas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: chartData.labels, // Label dari data
                    datasets: chartData.datasets, // Dataset dari controller
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }, // Tampilkan legend
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: Rp ${value.toLocaleString('id-ID')}`;
                                }
                            }
                        },
                    },
                    title: {
                        display: true,
                        text: title, // Judul chart
                    },
                },
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

</script>

