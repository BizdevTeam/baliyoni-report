<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Grafik Laporan Paket Administrasi -->
    <div class="p-4">
        <h1 class="text-4xl font-bold text-red-600">Dash<span class="text-red-600">board</span></h1>
        <div class="flex justify-end mb-4">

            <form id="chartFilterForm" method="GET" action="#" class="flex items-center justify-end gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input type="text" id="searchInput" name="search" placeholder="Search YYYY - MM" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                </div>
                <button type="submit" class="justify-end bg-gradient-to-r font-medium from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" aria-label="Search">
                    Search
                </button>
            </form>

        </div>
    </div>

    <!-- LAPORAN MARKETING -->
    <div id="gridContainer" class="grid gap-6 grid-cols-1">
        <!-- Card 1 -->

        <!-- LAPORAN MARKETING -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Penjualan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartp" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappenjualan.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Penjualan →</a>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Penjualan Perusahaan</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpp" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappenjualanperusahaan.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Penjualan Perusahaan →</a>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Paket Administrasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartl" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanpaketadministrasi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Paket Administrasi →</a>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Status Paket</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartsp" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("statuspaket.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Status Paket →</a>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Per Instansi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpi" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanperinstansi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Per Instansi →</a>
            </div>
        </div>

        <!-- LAPORAN PROCUREMENTS -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Pembelian (HOLDING)</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartph" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanholding.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Pembelian (HOLDING) →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Stok</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartls" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Stok →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Pembelian Outlet</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpo" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Pembelian Outlet →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Negosiasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartln" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Negosiasi →</a>
            </div>
        </div>

        <!-- LAPORAN SUPPORTS -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="w-full h-full max-w-[600px] max-h-[600px] mx-auto"> <!-- Container pembatas -->
                    <canvas id="chartlrp" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappendapatanservisasp.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Servis ASP →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Piutang Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="w-full h-full max-w-[600px] max-h-[600px] mx-auto"> <!-- Container pembatas -->
                    <canvas id="chartlrps" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route('rekappiutangservisasp.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Piutang Servis ASP →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Pengiriman Daerah Bali</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartrpdb" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansamitra.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman Daerah Bali →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Pengiriman Luar Bali</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartrplb" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporandetrans.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman Luar Bali →</a>
            </div>
        </div>

        <!-- LAPORAN HRGA -->

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Sakit</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="charts" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Sakit →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Izin</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartizin" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanizin.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Izin →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Cuti</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartcuti" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporancuti.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Cuti →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Terlambat</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartterlambat" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanterlambat.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Terlambat →</a>
            </div>
        </div>

         <!-- LAPORAN HRGA -->
         <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan PT BOS</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Responsive container -->
                    <table id="adminptbos" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Pekerjaan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Lalu</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Ini</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Update</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Rencana Implementasi</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('laporanptbos.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan PT BOS →</a>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan iJASA</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Responsive container -->
                    <table id="adminijasa" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Jam</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Permasalahan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Impact</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Troubleshooting</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Resolve Tanggal</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Resolve Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanijasa.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan iJASA →</a>
                </div>
            </div>
        </div>

        <!-- LAPORAN ACCOUNTING -->

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Kas Hutang Piutang Stok</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="w-full h-full max-w-[600px] max-h-[600px] mx-auto"> <!-- Container pembatas -->
                    <canvas id="chartkhps" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("khps.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Kas Hutang Piutang Stok →</a>
            </div>
        </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Arus Kas</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="w-full h-full max-w-[600px] max-h-[600px] mx-auto"> <!-- Container pembatas -->
                <canvas id="chartak" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("khps.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Arus Kas →</a>
            </div>
        </div>

         <!-- Laba Rugi -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Laba Rugi</h1>
        
            <!-- Container untuk menampilkan gambar -->
            <div id="image-container" class="flex flex-wrap gap-4 justify-center"></div>
        
            <div class="flex justify-end mt-4">
                <a href="{{ route('labarugi.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">
                    Laporan Laba Rugi →
                </a>
            </div>
        </div>  

        <!-- LAPORAN LABA RUGI -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Laba Rugi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminlabarugi" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('labarugi.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Laba Rugi →</a>
                </div>
            </div>
            <!-- Modal Gambar -->
            <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden">
                <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg">
            </div>
        </div>


        <!-- LAPORAN NERACA -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Neraca</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminneraca" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('neraca.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Neraca →</a>
                </div>
            </div>
        </div>

        <!-- LAPORAN IT -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan Bizdev</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminbizdev" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Aplikasi</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Lalu</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Bulan Ini</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Update</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Rencana Implementasi</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('laporanbizdev.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Bizdev →</a>
                </div>
            </div>
        </div>

        <!-- LAPORAN SPI -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan SPI Operasional</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                <table id="adminspi" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Aspek</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Masalah</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Solusi</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Implementasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanspi.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan SPI Operasional →</a>
                </div>
            </div>
        </div>
        <!-- LAPORAN SPI IT-->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan SPI IT</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                <table id="adminspiti" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-center">Bulan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Aspek</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Masalah</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Solusi</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Implementasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route("laporanspiti.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan SPI IT →</a>
                </div>
            </div>
        </div>


    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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

        //laporan ACCOUNTING
        fetchChartPieData('{{ route("adminkhps.chart.data") }}', 'chartkhps', 'Nilai Pendapatan ');
        fetchChartPieData('{{ route("adminak.chart.data") }}', 'chartak', 'Nilai Piutang ');


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

        //laporan ACCOUNTING
        fetchChartPieData('{{ route("adminkhps.chart.data") }}' + queryString, 'chartkhps');
        fetchChartPieData('{{ route("adminak.chart.data") }}' + queryString, 'chartak');

    });


    function fetchChartData(url, canvasId, title) {
        fetch(url)
            .then(response => response.json())
            .then(chartData => {
                let chartCanvas = document.getElementById(canvasId);
                if (chartCanvas.chart) {
                    chartCanvas.chart.destroy();
                }

                chartData.labels = chartData.labels.slice(0, 12);
                chartData.datasets.forEach(dataset => {
                dataset.data = dataset.data.slice(0, 12);
            });
                chartCanvas.chart = new Chart(chartCanvas.getContext('2d'), {
                    type: 'bar'
                    , data: {
                        labels: chartData.labels
                        , datasets: chartData.datasets
                    , }
                    , options: {
                        responsive: true
                        , plugins: {
                            legend: {
                                display: false
                            }
                        , }
                        , scales: {
                            x: {
                                title: {
                                    display: true
                                    , text: title
                                }
                            }
                            , y: {
                                beginAtZero: true
                            }
                        , }
                    , }
                , });
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
                    type: 'pie'
                    , data: {
                        labels: chartData.labels, // Label dari data
                        datasets: chartData.datasets, // Dataset dari controller
                    }
                    , options: {
                        responsive: true
                        , plugins: {
                            legend: {
                                display: true
                            }, // Tampilkan legend
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        return `${label}: Rp ${value.toLocaleString('id-ID')}`;
                                    }
                                }
                            }
                        , }
                        , title: {
                            display: true
                            , text: title, // Judul chart
                        }
                    , }
                , });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    //tabel laporan pt bos
    $(document).ready(function() {
        function fetchLaporanPTBOS(search = '') {
            $.ajax({
                url: "{{ route('laporanptbos.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminptbos tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanptboss.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanptboss.data.forEach(function(item) {
                            // Konversi format bulan dari 'YYYY-MM' ke 'Januari 2024'
                            const [tahun, bulan] = item.bulan.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                                , 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedBulan = `${namaBulan} ${tahun}`; // Gabungkan nama bulan dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedBulan}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.pekerjaan}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.kondisi_bulanlalu}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.kondisi_bulanini}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.update}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.rencana_implementasi}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporanPTBOS();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanPTBOS(searchValue);
        });
    });

    //tabel laporan iJASa
    $(document).ready(function() {
        function fetchLaporaniJASA(search = '') {
            $.ajax({
                url: "{{ route('laporanijasa.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminijasa tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanneracas.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanneracas.data.forEach(function(item) {
                            let row = `
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.tanggal}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.jam}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.permasalahan}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.impact}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.troubleshooting}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.resolve_tanggal}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.resolve_jam}</td>
                            </tr>
                        `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporaniJASA();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporaniJASA(searchValue);
        });
    });

    //tabel laporan BIZDEV
    $(document).ready(function() {
        function fetchLaporanBizdev(search = '') {
            $.ajax({
                url: "{{ route('laporanbizdev.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminbizdev tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanbizdevs.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanbizdevs.data.forEach(function(item) {
                            // Konversi format bulan dari 'YYYY-MM' ke 'Januari 2024'
                            const [tahun, bulan] = item.bulan.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                                , 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedBulan = `${namaBulan} ${tahun}`; // Gabungkan nama bulan dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedBulan}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.aplikasi}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.kondisi_bulanlalu}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.kondisi_bulanini}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.update}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.rencana_implementasi}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporanBizdev();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanBizdev(searchValue);
        });
    });

    
    //tabel laporan NERACA
    $(document).ready(function() {
        function fetchLaporanNeraca(search = '') {
            $.ajax({
                url: "{{ route('neraca.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminneraca tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanneracas.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanneracas.data.forEach(function(item) {
                            let row = `
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.bulan}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.gambar}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                            </tr>
                        `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporanNeraca();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanNeraca(searchValue);
        });
    });

    $(document).ready(function() {
    function fetchLaporanLabaRugi(search = '') {
        $.ajax({
            url: "{{ route('labarugi.index') }}",
            type: "GET",
            data: { search: search },
            dataType: "json",
            success: function(response) {
                let tableBody = $("#adminlabarugi tbody");
                tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                if (response.laporanlabarugis.data.length === 0) {
                    tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                } else {
                    response.laporanlabarugis.data.forEach(function(item, index) {
                        let row = `
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.bulan}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer item-center justify-center" data-index="${index}">
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });

                    // Event listener untuk memperbesar gambar saat diklik
                    $(".cursor-pointer").on("click", function(e) {
                        let imgSrc = $(this).attr("src");
                        $("#modalImage").attr("src", imgSrc);
                        $("#imageModal").fadeIn();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    // Jalankan fungsi saat halaman dimuat
    fetchLaporanLabaRugi();

    // Event listener untuk form pencarian
    $("#chartFilterForm").on("submit", function(e) {
        e.preventDefault(); // Mencegah form melakukan reload halaman
        let searchValue = $("#searchInput").val();
        fetchLaporanLabaRugi(searchValue);
    });

    // Event untuk menutup modal saat klik di luar gambar
    $("#imageModal").on("click", function(e) {
        if (!$(e.target).closest("#modalImage").length) {
            $(this).fadeOut();
        }
    });
});



    //tabel laporan SPI OPERASIONAL
    $(document).ready(function() {
        function fetchLaporanSPI(search = '') {
            $.ajax({
                url: "{{ route('laporanspi.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminspi tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanspis.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanspis.data.forEach(function(item) {
                            // Konversi format bulan dari 'YYYY-MM' ke 'Januari 2024'
                            const [tahun, bulan] = item.bulan.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                                , 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedBulan = `${namaBulan} ${tahun}`; // Gabungkan nama bulan dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedBulan}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.aspek}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.masalah}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.solusi}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.implementasi}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporanSPI();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanSPI(searchValue);
        });
    });

    //tabel laporan SPI IT
    $(document).ready(function() {
        function fetchLaporanSPITI(search = '') {
            $.ajax({
                url: "{{ route('laporanspiti.index') }}"
                , type: "GET"
                , data: {
                    search: search
                }, // Kirim parameter search ke server
                dataType: "json"
                , success: function(response) {
                    let tableBody = $("#adminspiti tbody");
                    tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                    if (response.laporanspitis.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanspitis.data.forEach(function(item) {
                            // Konversi format bulan dari 'YYYY-MM' ke 'Januari 2024'
                            const [tahun, bulan] = item.bulan.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                                , 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedBulan = `${namaBulan} ${tahun}`; // Gabungkan nama bulan dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedBulan}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.aspek}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.masalah}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.solusi}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.implementasi}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        fetchLaporanSPITI();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanSPITI(searchValue);
        });
    });

    $(document).ready(function () {
    fetchImages();
});

function fetchImages() {
    $.ajax({
        url: "{{ route('adminlabarugi.gambar') }}", // Route yang diperbaiki
        type: "GET",
        
        dataType: "json",
        success: function (data) {
            const container = $("#image-container");
            container.empty();

            if (data.length === 0) {
                container.html(`<div class="w-full text-center py-4">
                                  <span class="text-gray-500 text-lg">Tidak ada gambar tersedia</span>
                                </div>`);
                return;
            }

            data.forEach(item => {
                if (item.gambar) {
                    let img = `<img src="${item.gambar}" 
             alt="Thumbnail" 
             class="w-48 h-48 object-cover rounded-lg shadow-lg cursor-pointer hover:scale-105 transition-transform">`;                    container.append(img);
                }
            });
        },
        error: function (xhr, status, error) {
            console.error("Error:", status, error);
            console.error("Detail:", xhr.responseText);
            $("#image-container").html(`<div class="text-red-500">Gagal memuat gambar</div>`);
        }
    });
}


</script>

