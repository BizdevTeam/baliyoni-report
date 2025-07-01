<div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
    <!-- Grafik Laporan Paket Administrasi -->
    <div class="p-4 ">
        <h1 class="mt-10 text-4xl font-bold text-red-600">Dash<span class="text-black">board</span></h1>
        <div class="flex justify-end mb-4">

            <form id="chartFilterForm" method="GET" action="#" class="flex items-center justify-end gap-2">
                <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                    <input type="date" id="searchInput" name="search" placeholder="Search YYYY - MM" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
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
                <a href="{{ route("laporanstok.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Stok →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Pembelian Outlet</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpo" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanholding.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Pembelian Outlet →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Negosiasi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartln" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporannegosiasi.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Negosiasi →</a>
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
        {{-- <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Pengiriman Daerah Bali</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartrpdb" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansamitra.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman Daerah Bali →</a>
            </div>
        </div> --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pengiriman</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartrplb" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporandetrans.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman→</a>
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
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Pekerjaan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Tanggal Lalu</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Tanggal Ini</th>
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

        <!-- LAPORAN iJASA Gambar-->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan iJASA Gambar</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminijasagambar" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('ijasagambar.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan iJASA Gambar →</a>
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

        <!-- LAPORAN LABA RUGI -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Laba Rugi</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminlabarugi" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
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
            <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
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
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
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
                  <!-- Modal Gambar -->
                  <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                    <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                </div>
             </div>

        <!-- LAPORAN RASIO -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Rasio</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminrasio" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('rasio.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rasio →</a>
                </div>
            </div>
                  <!-- Modal Gambar -->
                  <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                    <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                </div>
             </div>

        <!-- LAPORAN PPN -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan PPN</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminppn" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('laporanppn.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan PPN →</a>
                </div>
            </div>
                  <!-- Modal Gambar -->
                  <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                    <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                </div>
             </div>

        <!-- LAPORAN TAX PLANNING -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Laporan Tax Planning</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="admintaxplanning" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('taxplaning.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Tax Planning →</a>
                </div>
            </div>
                  <!-- Modal Gambar -->
                  <div id="imageModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 hidden z-50">
                    <img id="modalImage" class="mx-auto my-auto object-center max-w-full max-h-[90vh] rounded-lg shadow-lg z-50">
                </div>
             </div>

        <!-- LAPORAN IT Multimedia Tiktok-->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan Multimedia Tiktok</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="admintiktok" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('tiktok.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Multimedia Tiktok →</a>
                </div>
            </div>
        </div>

        <!-- LAPORAN IT Multimedia Instagram-->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan Multimedia Instagram</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="admininstagram" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('multimediainstagram.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Multimedia Instagram →</a>
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
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Aplikasi</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Tanggal Lalu</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Kondisi Tanggal Ini</th>
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

        <!-- LAPORAN IT Bizdev Gambar -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Tabel Laporan Bizdev Gambar</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="max-w-[600px] md:max-w-none mx-auto md:mx-0 overflow-x-auto"> <!-- Container pembatas dan scroll -->
                    <table id="adminbizdevgambar" class="table-auto w-full border-collapse border border-gray-300 min-w-[600px] md:min-w-full">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">File</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('laporanbizdevgambar.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Bizdev Gambar →</a>
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
                            <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
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
                            <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
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

<button id="exportFloatingButton" class="fixed bottom-48 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <mask id="lineMdCloudAltPrintFilledLoop0">
            <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path stroke-dasharray="64" stroke-dashoffset="64" d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                    <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                </path>
                <g fill="#fff" stroke="none" opacity="0">
                    <circle cx="12" cy="10" r="6">
                        <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                    </circle>
                    <rect width="9" height="8" x="8" y="12" />
                    <rect width="15" height="12" x="1" y="8" rx="6">
                        <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="1;0;1;2;1" />
                    </rect>
                    <rect width="13" height="10" x="10" y="10" rx="5">
                        <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="10;9;10;11;10" />
                    </rect>
                    <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                </g>
                <g fill="#000" fill-opacity="0" stroke="none">
                    <circle cx="12" cy="10" r="4">
                        <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                    </circle>
                    <rect width="9" height="6" x="8" y="12" />
                    <rect width="11" height="8" x="3" y="10" rx="4">
                        <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="3;2;3;4;3" />
                    </rect>
                    <rect width="9" height="6" x="12" y="12" rx="3">
                        <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="12;11;12;13;12" />
                    </rect>
                    <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                    <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                </g>
                <g stroke="none">
                    <path fill="#fff" d="M6 11h12v0h-12z">
                        <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s" values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                    </path>
                    <path fill="#000" d="M8 13h8v0h-8z">
                        <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s" values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                    </path>
                    <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                        <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                        <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2" repeatCount="indefinite" />
                    </path>
                </g>
            </g>
        </mask>
        <rect width="30" height="30" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
    </svg>
</button>

<div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Export PDF</h2>
        <p class="mb-4">Apakah Anda ingin mengekspor laporan penjualan ke PDF?</p>
        <div class="flex justify-end space-x-2">
            <button id="cancelExportBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
            <button id="confirmExportBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor PDF</button>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loadingIndicator" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white"></div>
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
        fetchChartDataWRp('{{ route("adminpenjualan.chart.data") }}', 'chartp', 'Tanggal ');
        fetchChartDataWRp('{{ route("adminpp.chart.data") }}', 'chartpp', 'Perusahaan ');
        fetchChartDataWRp('{{ route("admin.chart.data") }}', 'chartl', 'Nilai Paket ');
        fetchChartDataNRp('{{ route("adminstatuspaket.chart.data") }}', 'chartsp', 'Nilai Paket ');
        fetchChartDataNRp('{{ route("adminperinstansi.chart.data") }}', 'chartpi', 'Nilai Paket ');

        //laporan PROCUREMENTS
        fetchChartDataWRp('{{ route("adminholding.chart.data") }}', 'chartph', 'Perusahaan');
        fetchChartDataWRp('{{ route("adminstok.chart.data") }}', 'chartls', 'Tanggal ');
        fetchChartDataWRp('{{ route("adminoutlet.chart.data") }}', 'chartpo', 'Nilai Pembelian ');
        fetchChartDataWRp('{{ route("adminnegosiasi.chart.data") }}', 'chartln', 'Nilai Negosiasi ');

        //laporan SUPPORTS
        fetchChartPieData('{{ route("adminpendapatanservisasp.chart.data") }}', 'chartlrp', 'Nilai Pendapatan ');
        fetchChartPieData('{{ route("adminpiutangservisasp.chart.data") }}', 'chartlrps', 'Nilai Piutang ');
        fetchChartDataWRp('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}', 'chartrplb', 'Nilai Pendapatan ');

        //laporan HRGA
        fetchChartDataNRp('{{ route("adminsakit.chart.data") }}', 'charts', 'Nama Karyawan');
        fetchChartDataNRp('{{ route("adminizin.chart.data") }}', 'chartizin', 'Nama Karyawan');
        fetchChartDataNRp('{{ route("admincuti.chart.data") }}', 'chartcuti', 'Nama Karyawan');
        fetchChartDataNRp('{{ route("adminterlambat.chart.data") }}', 'chartterlambat', 'Nama Karyawan');

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
        fetchChartDataWRp('{{ route("adminpenjualan.chart.data") }}' + queryString, 'chartp');
        fetchChartDataWRp('{{ route("adminpp.chart.data") }}' + queryString, 'chartpp');
        fetchChartDataWRp('{{ route("admin.chart.data") }}' + queryString, 'chartl');
        fetchChartDataNRp('{{ route("adminstatuspaket.chart.data") }}' + queryString, 'chartsp');
        fetchChartDataNRp('{{ route("adminperinstansi.chart.data") }}' + queryString, 'chartpi');

        //laporan PROCUREMENTS
        fetchChartDataWRp('{{ route("adminholding.chart.data") }}' + queryString, 'chartph');
        fetchChartDataWRp('{{ route("adminstok.chart.data") }}' + queryString, 'chartls');
        fetchChartDataWRp('{{ route("adminoutlet.chart.data") }}' + queryString, 'chartpo');
        fetchChartDataWRp('{{ route("adminnegosiasi.chart.data") }}' + queryString, 'chartln');

        //laporan SUPPORTS
        fetchChartPieData('{{ route("adminpendapatanservisasp.chart.data") }}' + queryString, 'chartlrp');
        fetchChartPieData('{{ route("adminpiutangservisasp.chart.data") }}' + queryString, 'chartlrps');
        fetchChartDataWRp('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}' + queryString, 'chartrplb');

        //laporan HRGA
        fetchChartDataNRp('{{ route("adminsakit.chart.data") }}' + queryString, 'charts');
        fetchChartDataNRp('{{ route("adminizin.chart.data") }}' + queryString, 'chartizin');
        fetchChartDataNRp('{{ route("admincuti.chart.data") }}' + queryString, 'chartcuti');
        fetchChartDataNRp('{{ route("adminterlambat.chart.data") }}' + queryString, 'chartterlambat');

        //laporan ACCOUNTING
        fetchChartPieData('{{ route("adminkhps.chart.data") }}' + queryString, 'chartkhps');
        fetchChartPieData('{{ route("adminak.chart.data") }}' + queryString, 'chartak');

    });

    //fetch menggunakan Rp
    function fetchChartDataWRp(url, canvasId, title) {
    fetch(url)
        .then(response => response.json())
        .then(chartData => {
            let chartCanvas = document.getElementById(canvasId);
            if (chartCanvas.chart) {
                chartCanvas.chart.destroy();
            }

            // Batasi jumlah label dan data
            chartData.labels = chartData.labels.slice(0, 12);
            chartData.datasets.forEach(dataset => {
                dataset.data = dataset.data.slice(0, 12);
            });

            chartCanvas.chart = new Chart(chartCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
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
                                text: title
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: {
                        onComplete: function() {
                            var ctx = chartCanvas.chart.ctx;
                            ctx.font = 'bold 15px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = 'black';
                            
                            chartCanvas.chart.data.datasets.forEach((dataset, i) => {
                                var meta = chartCanvas.chart.getDatasetMeta(i);
                                meta.data.forEach((bar, index) => {
                                    var value = dataset.data[index];
                                    ctx.fillText('Rp ' +  value.toLocaleString(), bar.x, bar.y - 10);
                                });
                            });
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
    }

    //fetch chart tanpa Rp
    function fetchChartDataNRp(url, canvasId, title) {
    fetch(url)
        .then(response => response.json())
        .then(chartData => {
            let chartCanvas = document.getElementById(canvasId);
            if (chartCanvas.chart) {
                chartCanvas.chart.destroy();
            }

            // Batasi jumlah label dan data
            chartData.labels = chartData.labels.slice(0, 12);
            chartData.datasets.forEach(dataset => {
                dataset.data = dataset.data.slice(0, 12);
            });

            chartCanvas.chart = new Chart(chartCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
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
                                text: title
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: {
                        onComplete: function() {
                            var ctx = chartCanvas.chart.ctx;
                            ctx.font = 'bold 15px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = 'black';
                            
                            chartCanvas.chart.data.datasets.forEach((dataset, i) => {
                                var meta = chartCanvas.chart.getDatasetMeta(i);
                                meta.data.forEach((bar, index) => {
                                    var value = dataset.data[index];
                                    ctx.fillText(value.toLocaleString(), bar.x, bar.y - 10);
                                });
                            });
                        }
                    }
                }
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
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                            const [tahun, bulan, hari] = item.tanggal.split('-');

                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
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

    //tabel laporan iJASA
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

                    if (response.laporanijasas.data.length === 0) {
                        tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanijasas.data.forEach(function(item) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                            const [tahun, bulan, hari] = item.date.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun
                            let row = `
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
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
                               // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                               const [tahun, bulan, hari] = item.tanggal.split('-');
                                const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
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

    //laporan neraca
    $(document).ready(function() {
        function fetchLaporanNeraca(search = '') {
            $.ajax({
                url: "{{ route('neraca.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminneraca tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporanneracas.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanneracas.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                           const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanNeraca();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanNeraca(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    //laporan laba rugi
        $(document).ready(function() {
        function fetchLaporanLabaRugi(search = '') {
            $.ajax({
                url: "{{ route('labarugi.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminlabarugi tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporanlabarugis.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanlabarugis.data.forEach(function(item, index) {
                            const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array
                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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

    //laporan rasio
        $(document).ready(function() {
        function fetchLaporanRasio(search = '') {
            $.ajax({
                url: "{{ route('rasio.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminrasio tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporanrasios.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanrasios.data.forEach(function(item, index) {
                            const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array
                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanRasio();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanRasio(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    //laporan ppn
        $(document).ready(function() {
        function fetchLaporanPPn(search = '') {
            $.ajax({
                url: "{{ route('laporanppn.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminppn tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporanppns.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanppns.data.forEach(function(item, index) {
                                // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                                const [tahun, bulan, hari] = item.tanggal.split('-');
                                const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanPPn();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanPPn(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    //laporan tax planning
        $(document).ready(function() {
        function fetchLaporanTaxPlanning(search = '') {
            $.ajax({
                url: "{{ route('taxplaning.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#admintaxplanning tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporantaxplanings.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporantaxplanings.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                                const [tahun, bulan, hari] = item.tanggal.split('-');
                                const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanTaxPlanning();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanTaxPlanning(searchValue);
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
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                            const [tahun, bulan, hari] = item.tanggal.split('-');

                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun
                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
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
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                            const [tahun, bulan, hari] = item.tanggal.split('-');

                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                            const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            // Buat baris tabel dengan formattedBulan
                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
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
                container.html(`<div class="w-full text-center items-center align-center py-4">
                                  <span class="text-gray-500 text-lg">Tidak ada gambar tersedia</span>
                                </div>`);
                return;
            }

            data.forEach(item => {
                if (item.gambar) {
                    let img = `<div class="flex justify-center items-center"> <!-- Tambahkan div wrapper untuk centering -->
                                <img src="${item.gambar}" 
                                    alt="Thumbnail" 
                                    class="w-48 h-48 object-cover rounded-lg shadow-lg cursor-pointer hover:scale-105 transition-transform mx-auto">
                            </div>`;
                    container.append(img);
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

    //laporan ijasa gambar 
    $(document).ready(function() {
        function fetchLaporaniJASAGambar(search = '') {
            $.ajax({
                url: "{{ route('ijasagambar.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminijasagambar tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.ijasagambars.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.ijasagambars.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                           const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporaniJASAGambar();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporaniJASAGambar(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    //laporan multimedia tiktok
    $(document).ready(function() {
        function fetchLaporanTiktok(search = '') {
            $.ajax({
                url: "{{ route('tiktok.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#admintiktok tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.itmultimediatiktoks.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.itmultimediatiktoks.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                           const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanTiktok();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanTiktok(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });
    
    //laporan multimedia instagram
    $(document).ready(function() {
        function fetchLaporanInstagram(search = '') {
            $.ajax({
                url: "{{ route('multimediainstagram.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#admininstagram tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.itmultimediainstagrams.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.itmultimediainstagrams.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                           const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanInstagram();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanInstagram(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    //laporan bizdev gambar
    $(document).ready(function() {
        function fetchLaporanBizdevGambar(search = '') {
            $.ajax({
                url: "{{ route('laporanbizdevgambar.index') }}",
                type: "GET",
                data: { search: search },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminbizdevgambar tbody");
                    tableBody.empty(); // Clear table before adding new data

                    if (response.laporanbizdevgambars.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanbizdevgambars.data.forEach(function(item, index) {
                            // Konversi format Tanggal dari 'YYYY-MM-DD' ke '25 Januari 2024'
                           const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

                                const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`; // Gabungkan hari, bulan, dan tahun

                            let row = `
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <img src="${item.gambar_url}" alt="Laporan Gambar" class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer block mx-auto" data-index="${index}">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">${item.keterangan}</td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });

                        // Event listener for enlarging the image on click
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
        fetchLaporanBizdevGambar();

        // Event listener untuk form pencarian
        $("#chartFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val();
            fetchLaporanBizdevGambar(searchValue);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });





// Fungsi untuk menampilkan atau menyembunyikan modal
function toggleModal() {
    document.getElementById('exportModal').classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const exportFloatingButton = document.getElementById('exportFloatingButton');
    const exportModal = document.getElementById('exportModal');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const confirmExportBtn = document.getElementById('confirmExportBtn');

    // Event listener untuk tombol floating & modal
    exportFloatingButton.addEventListener('click', toggleModal);
    cancelExportBtn.addEventListener('click', toggleModal);
    
    confirmExportBtn.addEventListener('click', function() {
        toggleModal(); // Tutup modal setelah konfirmasi
        triggerPDFExport(); // Panggil fungsi ekspor
    });
});

// Fungsi untuk melakukan ekspor PDF
function triggerPDFExport() {
    const routes = [
        "/rekappenjualanperusahaan/export-pdf",
        "/rekappenjualan/export-pdf",
        "/laporanpaketadministrasi/export-pdf",
        "/statuspaket/export-pdf",
        "/laporanperinstansi/export-pdf"
    ];

    routes.forEach(route => {
        fetch(route, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.blob();
        })
        .then(blob => {
            if (blob.size === 0) {
                throw new Error("File PDF kosong! Periksa kembali data yang diekspor.");
            }

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "export.pdf";
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => console.error("Error exporting PDF:", error));
    });
}

function toggleModal() {
    const exportModal = document.getElementById('exportModal');
    if (exportModal) {
        exportModal.classList.toggle('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const exportButton = document.getElementById('exportButton'); // Existing export button
    const exportModal = document.getElementById('exportModal');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const confirmExportBtn = document.getElementById('confirmExportBtn');

    // Function to show modal
    function showExportModal() {
        exportModal.classList.remove('hidden');
    }

    // Function to hide modal
    function hideExportModal() {
        exportModal.classList.add('hidden');
    }

    // Event listener for export button to show modal
    if (exportButton) {
        exportButton.addEventListener('click', showExportModal);
    }

    // Cancel button closes the modal
    cancelExportBtn.addEventListener('click', hideExportModal);

    // Confirm export button
    confirmExportBtn.addEventListener('click', function() {
        // Hide modal
        hideExportModal();

        // Dispatch custom event to trigger export
        document.dispatchEvent(new Event('triggerPDFExport'));
    });

    // Optional: Close modal if clicking outside
    exportModal.addEventListener('click', function(event) {
        if (event.target === exportModal) {
            hideExportModal();
        }
    });
});


//export pdf 
async function exportToPDF1() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Pastikan meta tag CSRF disertakan.');
        return;
    }

    // Ambil data dari tabel
    const items = Array.from(document.querySelectorAll('#data-table tr')).map(row => {
        const cells = row.querySelectorAll('td');
        return {
            tanggal: cells[0]?.innerText.trim() || '',
            perusahaan: cells[1]?.innerText.trim() || '',
            total_penjualan_formatted: cells[2]?.innerText.trim() || '',
        };
    });

    const tableContent = items
        .filter(item => item.tanggal && item.perusahaan && item.total_penjualan_formatted)
        .map(item => `
            <tr>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.tanggal}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.perusahaan}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.total_penjualan_formatted}</td>
            </tr>
        `).join('');

    const chartCanvas = document.querySelector('#chartpp');
    if (!chartCanvas) {
        alert('Elemen canvas grafik tidak ditemukan.');
        return;
    }

    const chartBase64 = chartCanvas.toDataURL();

    try {
        const response = await fetch('/exportall', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                table2: tableContent,
                chart2: chartBase64,
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Laporan_rekap_penjualan_perusahaan.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            const errorData = await response.json();
            alert(errorData.message || 'Gagal mengekspor PDF.');
        }
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('Terjadi kesalahan saat mengekspor PDF.');
    }
}

// Add event listener to trigger export


</script>

