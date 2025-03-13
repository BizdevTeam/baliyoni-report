@if (Auth::check())
<div class="user-info mb-4">
    <div id="admincontent" class="content-wrapper ml-72 p-4 transition-all duration-300">
        <!-- Grafik Laporan Paket Administrasi -->
        <div class="p-4 ">
            <h1 class="mt-10 text-4xl font-bold text-red-600">Dash<span class="text-red-600">board</span></h1>
            <div class="flex justify-end mb-4 gap-4">
                <!-- Search by Date -->
                <form id="dateFilterForm" method="GET" action="#" class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-700 rounded-lg p-2 max-w-md">
                        <input type="date" id="searchInput" name="search" placeholder="Search YYYY - MM" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" aria-label="Search Date">
                        Search
                    </button>
                </form>
            </div>
        </div>

    <!-- LAPORAN MARKETING -->
    <div id="gridContainer" class="grid gap-6 grid-cols-1">
        <!-- MARKETING: Tampil untuk Superadmin & Marketing -->
        @if(in_array(Auth::user()->role, ['superadmin', 'marketing']))
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
        @endif

        <!-- PROCUREMENT: Tampil untuk Superadmin & Procurement -->
        @if(in_array(Auth::user()->role, ['superadmin', 'procurement']))
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
        @endif

        <!-- MARKETING: Tampil untuk Superadmin & Marketing -->
        @if(in_array(Auth::user()->role, ['superadmin', 'support']))
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pendapatan Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas id="chartlrp" class="w-full h-96"></canvas>
                </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappendapatanservisasp.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Servis ASP →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Piutang Servis ASP</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                    <canvas id="chartlrps" class="w-full h-96"></canvas>
                </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route('rekappiutangservisasp.index') }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Piutang Servis ASP →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Pengiriman</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartrplb" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporandetrans.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Pengiriman→</a>
            </div>
        </div>
        @endif

        <!-- LAPORAN ACCOUNTING -->
         <!-- ACCOUNTING: Tampil untuk Superadmin & Accounting -->
         @if(in_array(Auth::user()->role, ['superadmin', 'accounting']))
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
                <a href="{{ route("aruskas.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Arus Kas →</a>
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
             @endif

        <!-- IT: Tampil untuk Superadmin & IT -->
        @if(in_array(Auth::user()->role, ['superadmin', 'it']))
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
        @endif

         <!-- HRGA & Lainnya -->
         @if(in_array(Auth::user()->role, ['superadmin', 'hrga']))
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
        @endif

        <!-- SPI: Tampil untuk Superadmin & SPI -->
        @if(in_array(Auth::user()->role, ['superadmin', 'spi']))
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
        @endif
    </div>
</div>
</div>
@endif



<!-- Loading Indicator -->
<div id="loadingIndicator" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white"></div>
</div>

<x-floating-popover/>

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
        fetchChartDataWRp('{{ route("adminpendapatanservisasp.chart.data") }}', 'chartlrp', 'Nilai Pendapatan ');
        fetchChartDataWRp('{{ route("adminpiutangservisasp.chart.data") }}', 'chartlrps', 'Nilai Piutang ');
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

    document.addEventListener("DOMContentLoaded", loadInitialChartData);

    function fetchCharts(queryParams = {}) {
        let queryString = new URLSearchParams(queryParams).toString();
        queryString = queryString ? `?${queryString}` : '';

        // laporan MARKETING
        fetchChartDataWRp('{{ route("adminpenjualan.chart.data") }}' + queryString, 'chartp');
        fetchChartDataWRp('{{ route("adminpp.chart.data") }}' + queryString, 'chartpp');
        fetchChartDataWRp('{{ route("admin.chart.data") }}' + queryString, 'chartl');
        fetchChartDataNRp('{{ route("adminstatuspaket.chart.data") }}' + queryString, 'chartsp');
        fetchChartDataNRp('{{ route("adminperinstansi.chart.data") }}' + queryString, 'chartpi');

        // laporan PROCUREMENTS
        fetchChartDataWRp('{{ route("adminholding.chart.data") }}' + queryString, 'chartph');
        fetchChartDataWRp('{{ route("adminstok.chart.data") }}' + queryString, 'chartls');
        fetchChartDataWRp('{{ route("adminoutlet.chart.data") }}' + queryString, 'chartpo');
        fetchChartDataWRp('{{ route("adminnegosiasi.chart.data") }}' + queryString, 'chartln');

        // laporan SUPPORTS
        fetchChartDataWRp('{{ route("adminpendapatanservisasp.chart.data") }}' + queryString, 'chartlrp');
        fetchChartDataWRp('{{ route("adminpiutangservisasp.chart.data") }}' + queryString, 'chartlrps');
        fetchChartDataWRp('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}' + queryString, 'chartrplb');

        // laporan HRGA
        fetchChartDataNRp('{{ route("adminsakit.chart.data") }}' + queryString, 'charts');
        fetchChartDataNRp('{{ route("adminizin.chart.data") }}' + queryString, 'chartizin');
        fetchChartDataNRp('{{ route("admincuti.chart.data") }}' + queryString, 'chartcuti');
        fetchChartDataNRp('{{ route("adminterlambat.chart.data") }}' + queryString, 'chartterlambat');

        // laporan ACCOUNTING
        fetchChartPieData('{{ route("adminkhps.chart.data") }}' + queryString, 'chartkhps');
        fetchChartPieData('{{ route("adminak.chart.data") }}' + queryString, 'chartak');
    }

    document.getElementById('dateFilterForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let searchValue = document.getElementById('searchInput').value.trim();
        let queryParams = {};
        if (searchValue) {
            queryParams['search'] = searchValue;
        }
        fetchCharts(queryParams);
    });

    document.getElementById('monthFilterForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let startMonth = document.getElementById('startMonth').value;
        let endMonth = document.getElementById('endMonth').value;
        let queryParams = {};
        if (startMonth && endMonth) {
            queryParams['start_month'] = startMonth;
            queryParams['end_month'] = endMonth;
        }
        fetchCharts(queryParams);
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
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 15, // Ukuran font 15px
                                        weight: 'bold' // Membuat teks bold
                                    }
                                }
                            },
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
        function fetchLaporanPTBOS(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanptbos.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
                dataType: "json",
                success: function(response) {
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

        // Event listener untuk form pencarian dan filter (gabungan search, start_month, end_month)
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanPTBOS(searchValue, startMonth, endMonth);
        });
    });

    //tabel laporan iJASA
    $(document).ready(function() {
        function fetchLaporaniJASA(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanijasa.index') }}"
                , type: "GET"
                , data: {
                    search: search, start_month: start_month, end_month: end_month
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporaniJASA(searchValue, startMonth, endMonth);
        });
    });

    //tabel laporan BIZDEV
    $(document).ready(function() {
        function fetchLaporanBizdev(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanbizdev.index') }}"
                , type: "GET"
                , data: {
                    search: search, start_month: start_month, end_month: end_month
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanBizdev(searchValue, startMonth, endMonth);
        });
    });

    //laporan neraca
    $(document).ready(function() {
        function fetchLaporanNeraca(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('neraca.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanNeraca(searchValue, startMonth, endMonth);
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
        // Fungsi untuk mengambil data laporan laba rugi
        function fetchLaporanLabaRugi(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('labarugi.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#adminlabarugi tbody");
                    tableBody.empty(); // Bersihkan isi tabel

                    if (response.laporanlabarugis.data.length === 0) {
                        tableBody.append(`<tr><td colspan="3" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                    } else {
                        response.laporanlabarugis.data.forEach(function(item, index) {
                            const [tahun, bulan, hari] = item.tanggal.split('-');
                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ][parseInt(bulan, 10) - 1];
                            const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`;

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

                        // Event listener untuk memperbesar gambar saat diklik
                        $(".cursor-pointer").on("click", function() {
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

        // Event listener untuk form pencarian dan filter (gabungan search, start_month, end_month)
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanLabaRugi(searchValue, startMonth, endMonth);
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
        function fetchLaporanRasio(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('rasio.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanRasio(searchValue, startMonth, endMonth);
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
        function fetchLaporanPPn(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanppn.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanPPn(searchValue, startMonth, endMonth);
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
        function fetchLaporanTaxPlanning(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('taxplaning.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanTaxPlanning(searchValue, startMonth, endMonth);
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
        function fetchLaporanSPI(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanspi.index') }}"
                , type: "GET"
                , data: {
                    search: search, start_month: start_month, end_month: end_month 
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanSPI(searchValue, startMonth, endMonth);
        });
    });

    //tabel laporan SPI IT
    $(document).ready(function() {
        function fetchLaporanSPITI(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanspiti.index') }}"
                , type: "GET"
                , data: {
                    search: search, start_month: start_month, end_month: end_month 
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanSPITI(searchValue, startMonth, endMonth);
        });
    });

    $(document).ready(function () {
    fetchImages();
});

    //laporan ijasa gambar 
    $(document).ready(function() {
        function fetchLaporaniJASAGambar(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('ijasagambar.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporaniJASAGambar(searchValue, startMonth, endMonth);
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
        function fetchLaporanTiktok(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('tiktok.index') }}",
                type: "GET",
                data: {  search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanTiktok(searchValue, startMonth, endMonth);
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
        function fetchLaporanInstagram(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('multimediainstagram.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month  },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanInstagram(searchValue, startMonth, endMonth);
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
        function fetchLaporanBizdevGambar(search = '', start_month = '', end_month = '') {
            $.ajax({
                url: "{{ route('laporanbizdevgambar.index') }}",
                type: "GET",
                data: { search: search, start_month: start_month, end_month: end_month },
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
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanBizdevGambar(searchValue, startMonth, endMonth);
        });

        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

// // Fungsi untuk menampilkan atau menyembunyikan modal
// function toggleModal() {
//     document.getElementById('exportModal').classList.toggle('hidden');
// }

// document.addEventListener('DOMContentLoaded', function() {
//     const exportFloatingButton = document.getElementById('exportFloatingButton');
//     const exportModal = document.getElementById('exportModal');
//     const cancelExportBtn = document.getElementById('cancelExportBtn');
//     const confirmExportBtn = document.getElementById('confirmExportBtn');

//     // Event listener untuk tombol floating & modal
//     exportFloatingButton.addEventListener('click', toggleModal);
//     cancelExportBtn.addEventListener('click', toggleModal);
    
//     confirmExportBtn.addEventListener('click', function() {
//         toggleModal(); // Tutup modal setelah konfirmasi
//         triggerPDFExport(); // Panggil fungsi ekspor
//     });
// });

// // Fungsi untuk melakukan ekspor PDF
// function triggerPDFExport() {
//     const routes = [
//         "/rekappenjualanperusahaan/export-pdf",
//         "/rekappenjualan/export-pdf",
//         "/laporanpaketadministrasi/export-pdf",
//         "/statuspaket/export-pdf",
//         "/laporanperinstansi/export-pdf"
//     ];

//     routes.forEach(route => {
//         fetch(route, {
//             method: "POST",
//             headers: {
//                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//                 "Content-Type": "application/json"
//             },
//             body: JSON.stringify({})
//         })
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error(`HTTP error! Status: ${response.status}`);
//             }
//             return response.blob();
//         })
//         .then(blob => {
//             if (blob.size === 0) {
//                 throw new Error("File PDF kosong! Periksa kembali data yang diekspor.");
//             }

//             const url = window.URL.createObjectURL(blob);
//             const a = document.createElement("a");
//             a.href = url;
//             a.download = "export.pdf";
//             document.body.appendChild(a);
//             a.click();
//             a.remove();
//         })
//         .catch(error => console.error("Error exporting PDF:", error));
//     });
// }


</script>

