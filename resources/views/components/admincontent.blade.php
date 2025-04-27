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
                        <input type="month" id="searchInput" name="search" placeholder="Search YYYY - MM" value="{{ request('search') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
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
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">
            Grafik Laporan Rekap Penjualan
        </h1>

            <div class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartp" class="w-full h-96"></canvas>
            </div>

            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappenjualan.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Rekap Penjualan →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600">
                Grafik Laporan Rekap Penjualan Perusahaan                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        jualan Perusahaan
            </h1>
            
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect" class="p-2 border border-gray-300 rounded">
                    <option value="chartpp">Chart Biasa</option>
                    <option value="chartpptotal">Chart Total</option>
                </select>
            </div>
            
            <!-- Container untuk Chart Biasa -->
            <div id="chartpContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpp" class="w-full h-96"></canvas>
            </div>
            
            <!-- Container untuk Chart PP, disembunyikan secara default -->
            <div id="chartpptotalContainer" class="bg-white shadow-md rounded-lg p-6 mt-10 hidden">
                <canvas id="chartpptotal" class="w-full h-96"></canvas>
            </div>
            
            <div class="flex justify-end mt-4">
                <a href="{{ route('rekappenjualanperusahaan.index') }}" class="text-red-600 font-semibold hover:underline">
                    Laporan Rekap Penjualan Perusahaan →
                </a>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Paket Administrasi</h1>
           
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect1" class="p-2 border border-gray-300 rounded">
                    <option value="chartl">Chart Biasa</option>
                    <option value="chartltotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartlContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartl" class="w-full h-96"></canvas>
            </div>

            <div id="chartltotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartltotal" class="w-full h-96"></canvas>
            </div>

            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanpaketadministrasi.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Paket Administrasi →</a>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Status Paket</h1>
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect2" class="p-2 border border-gray-300 rounded">
                    <option value="chartsp">Chart Biasa</option>
                    <option value="chartsptotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartspContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartsp" class="w-full h-96"></canvas>
            </div>

            <div id="chartsptotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartsptotal" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("statuspaket.index") }}" class="text-red-600 font-semibold hover:underline">Laporan Status Paket →</a>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Per Instansi</h1>
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect3" class="p-2 border border-gray-300 rounded">
                    <option value="chartpi">Chart Biasa</option>
                    <option value="chartpitotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartpiContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartpi" class="w-full h-96"></canvas>
            </div>

            <div id="chartpitotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartpitotal" class="w-full h-96"></canvas>
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
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect4" class="p-2 border border-gray-300 rounded">
                    <option value="chartph">Chart Biasa</option>
                    <option value="chartphtotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartphContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartph" class="w-full h-96"></canvas>
            </div>

            <div id="chartphtotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartphtotal" class="w-full h-96"></canvas>
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
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect5" class="p-2 border border-gray-300 rounded">
                    <option value="chartlrp">Chart Biasa</option>
                    <option value="chartlrptotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartlrpContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartlrp" class="w-full h-96"></canvas>
            </div>

            <div id="chartlrptotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartlrptotal" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("rekappendapatanservisasp.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Rekap Pendapatan Servis ASP →</a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Rekap Piutang Servis ASP</h1>
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect6" class="p-2 border border-gray-300 rounded">
                    <option value="chartlrps">Chart Biasa</option>
                    <option value="chartlrpstotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartlrpsContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartlrps" class="w-full h-96"></canvas>
            </div>

            <div id="chartlrpstotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartlrpstotal" class="w-full h-96"></canvas>
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
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect7" class="p-2 border border-gray-300 rounded">
                    <option value="charts">Chart Biasa</option>
                    <option value="chartstotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartsContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="charts" class="w-full h-96"></canvas>
            </div>

            <div id="chartstotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartstotal" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporansakit.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Sakit →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Izin</h1>
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect8" class="p-2 border border-gray-300 rounded">
                    <option value="chartizin">Chart Biasa</option>
                    <option value="chartizintotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartizinContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartizin" class="w-full h-96"></canvas>
            </div>

            <div id="chartizintotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartizintotal" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporanizin.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Izin →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Cuti</h1>
      <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
                <select id="chartSelect9" class="p-2 border border-gray-300 rounded">
                    <option value="chartcuti">Chart Biasa</option>
                    <option value="chartcutitotal">Chart Total</option>
                </select>
            </div>
            
            <div id="chartcutiContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartcuti" class="w-full h-96"></canvas>
            </div>

            <div id="chartcutitotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartcutitotal" class="w-full h-96"></canvas>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route("laporancuti.index") }}" class="flex text-red-600 content-end end-0 text-end font-semibold hover:underline">Laporan Cuti →</a>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 hover:border-red-600 transition duration-300">
            <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Grafik Laporan Terlambat</h1>
            <!-- Dropdown untuk memilih chart -->
            <div class="mb-2 flex justify-end">
            <select id="chartSelect10" class="p-2 border border-gray-300 rounded">
                <option value="chartterlambat">Chart Biasa</option>
                <option value="chartterlambattotal">Chart Total</option>
            </select>
            </div>
            
            <div id="chartterlambatContainer" class="bg-white shadow-md rounded-lg p-6">
                <canvas id="chartterlambat" class="w-full h-96"></canvas>
            </div>

            <div id="chartterlambattotalContainer" class="bg-white shadow-md rounded-lg p-6 hidden">
                <canvas id="chartterlambattotal" class="w-full h-96"></canvas>
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

    //Marketing
    // fungsi untuk memilih tampilan chart rekap penjualan perusahaan
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect");
        const chartpContainer = document.getElementById("chartpContainer");
        const chartpptotalContainer = document.getElementById("chartpptotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartpContainer.classList.toggle("hidden", selectedChart !== "chartpp");
            chartpptotalContainer.classList.toggle("hidden", selectedChart !== "chartpptotal");
        });
    });

    // fungsi untuk memilih tampilan chart laporan paket administrasi
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect1");
        const chartlContainer = document.getElementById("chartlContainer");
        const chartltotalContainer = document.getElementById("chartltotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartl");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartltotal");
        });
    });
    // fungsi untuk memilih tampilan chart laporan status paket
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect2");
        const chartlContainer = document.getElementById("chartspContainer");
        const chartltotalContainer = document.getElementById("chartsptotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartsp");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartsptotal");
        });
    });
    // fungsi untuk memilih tampilan chart laporan per instansi
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect3");
        const chartlContainer = document.getElementById("chartpiContainer");
        const chartltotalContainer = document.getElementById("chartpitotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartpi");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartpitotal");
        });
    });

    //procurements
    // fungsi untuk memilih tampilan chart laporan holding
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect4");
        const chartlContainer = document.getElementById("chartphContainer");
        const chartltotalContainer = document.getElementById("chartphtotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartph");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartphtotal");
        });
    });

    //supports
    // fungsi untuk memilih tampilan chart laporan pendapatan servis asp
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect5");
        const chartlContainer = document.getElementById("chartlrpContainer");
        const chartltotalContainer = document.getElementById("chartlrptotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartlrp");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartlrptotal");
        });
    });

    // fungsi untuk memilih tampilan chart laporan piutang servis asp
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect6");
        const chartlContainer = document.getElementById("chartlrpsContainer");
        const chartltotalContainer = document.getElementById("chartlrpstotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartlrps");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartlrpstotal");
        });
    });

    //HRGA
    // fungsi untuk memilih tampilan chart laporan sakit
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect7");    
        const chartlContainer = document.getElementById("chartsContainer");
        const chartltotalContainer = document.getElementById("chartstotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "charts");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartstotal");
        });
    });

    // fungsi untuk memilih tampilan chart laporan izin
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect8");    
        const chartlContainer = document.getElementById("chartizinContainer");
        const chartltotalContainer = document.getElementById("chartizintotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartizin");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartizintotal");
        });
    });

    // fungsi untuk memilih tampilan chart laporan cuti
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect9");    
        const chartlContainer = document.getElementById("chartcutiContainer");
        const chartltotalContainer = document.getElementById("chartcutitotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartcuti");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartcutitotal");
        });
    });

    // fungsi untuk memilih tampilan chart laporan cuti
    document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect10");    
        const chartlContainer = document.getElementById("chartterlambatContainer");
        const chartltotalContainer = document.getElementById("chartterlambattotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartlContainer.classList.toggle("hidden", selectedChart !== "chartterlambat");
            chartltotalContainer.classList.toggle("hidden", selectedChart !== "chartterlambattotal");
        });
    });

    // Fungsi untuk memuat data awal grafik saat halaman pertama kali dibuka
        function loadInitialChartData() {
        //laporan MARKETING
        fetchChartDataWRp('{{ route("adminpenjualan.chart.data") }}', 'chartp', 'Tanggal ');
        fetchChartDataWRp('{{ route("adminpp.chart.data") }}', 'chartpp', 'Perusahaan ');
        fetchChartDataWRp('{{ route("adminpptotal.chart.data") }}', 'chartpptotal', 'Perusahaan ');
        fetchChartPaket('{{ route("admin.chart.data") }}', 'chartl', 'Nilai Paket ');
        fetchChartPaket('{{ route("admintotal.chart.data") }}', 'chartltotal', 'Nilai Paket ');
        fetchChartPaket('{{ route("adminstatuspaket.chart.data") }}', 'chartsp', 'Nilai Paket ');
        fetchChartPaket('{{ route("adminstatuspakettotal.chart.data") }}', 'chartsptotal', 'Nilai Paket ');
        fetchChartDataWRp('{{ route("adminperinstansi.chart.data") }}', 'chartpi', 'Nilai Paket ');
        fetchChartDataWRp('{{ route("adminperinstansitotal.chart.data") }}', 'chartpitotal', 'Nilai Paket ');

        //laporan PROCUREMENTS
        fetchChartDataWRp('{{ route("adminholding.chart.data") }}', 'chartph', 'Perusahaan');
        fetchChartDataWRp('{{ route("adminholdingtotal.chart.data") }}', 'chartphtotal', 'Perusahaan');
        fetchChartDataWRp('{{ route("adminstok.chart.data") }}', 'chartls', 'Tanggal ');
        fetchChartDataWRp('{{ route("adminoutlet.chart.data") }}', 'chartpo', 'Nilai Pembelian ');
        fetchChartDataWRp('{{ route("adminnegosiasi.chart.data") }}', 'chartln', 'Nilai Negosiasi ');

        //laporan SUPPORTS
        fetchChartDataWRp('{{ route("adminpendapatanservisasp.chart.data") }}', 'chartlrp', 'Nilai Pendapatan ');
        fetchChartDataWRp('{{ route("adminpendapatanservisasptotal.chart.data") }}', 'chartlrptotal', 'Nilai Pendapatan ');
        fetchChartDataWRp('{{ route("adminpiutangservisasp.chart.data") }}', 'chartlrps', 'Nilai Piutang ');
        fetchChartDataWRp('{{ route("adminpiutangservisasptotal.chart.data") }}', 'chartlrpstotal', 'Nilai Piutang ');
        fetchChartDataWRp('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}', 'chartrplb', 'Nilai Pendapatan ');

        //laporan HRGA
        fetchChartHRGA1('{{ route("adminsakit.chart.data") }}', 'charts', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("adminsakittotal.chart.data") }}', 'chartstotal', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("adminizin.chart.data") }}', 'chartizin', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("adminizintotal.chart.data") }}', 'chartizintotal', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("admincuti.chart.data") }}', 'chartcuti', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("admincutitotal.chart.data") }}', 'chartcutitotal', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("adminterlambat.chart.data") }}', 'chartterlambat', 'Nama Karyawan');
        fetchChartHRGA1('{{ route("adminterlambattotal.chart.data") }}', 'chartterlambattotal', 'Nama Karyawan');

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
        fetchChartDataWRp('{{ route("adminpptotal.chart.data") }}' + queryString, 'chartpptotal');
        fetchChartPaket('{{ route("admin.chart.data") }}' + queryString, 'chartl');
        fetchChartPaket('{{ route("admintotal.chart.data") }}' + queryString, 'chartltotal');
        fetchChartPaket('{{ route("adminstatuspaket.chart.data") }}' + queryString, 'chartsp');
        fetchChartPaket('{{ route("adminstatuspakettotal.chart.data") }}' + queryString, 'chartsptotal');
        fetchChartDataNRp('{{ route("adminperinstansi.chart.data") }}' + queryString, 'chartpi');
        fetchChartDataNRp('{{ route("adminperinstansitotal.chart.data") }}' + queryString, 'chartpitotal');

        // laporan PROCUREMENTS
        fetchChartDataWRp('{{ route("adminholding.chart.data") }}' + queryString, 'chartph');
        fetchChartDataWRp('{{ route("adminholdingtotal.chart.data") }}' + queryString, 'chartphtotal');
        fetchChartDataWRp('{{ route("adminstok.chart.data") }}' + queryString, 'chartls');
        fetchChartDataWRp('{{ route("adminoutlet.chart.data") }}' + queryString, 'chartpo');
        fetchChartDataWRp('{{ route("adminnegosiasi.chart.data") }}' + queryString, 'chartln');

        // laporan SUPPORTS
        fetchChartDataWRp('{{ route("adminpendapatanservisasp.chart.data") }}' + queryString, 'chartlrp');
        fetchChartDataWRp('{{ route("adminpendapatanservisasptotal.chart.data") }}' + queryString, 'chartlrptotal');
        fetchChartDataWRp('{{ route("adminpiutangservisasp.chart.data") }}' + queryString, 'chartlrps');
        fetchChartDataWRp('{{ route("adminpiutangservisasptotal.chart.data") }}' + queryString, 'chartlrpstotal');
        fetchChartDataWRp('{{ route("adminpendapatanpengirimanluarbali.chart.data") }}' + queryString, 'chartrplb');

        // laporan HRGA
        fetchChartHRGA1('{{ route("adminsakit.chart.data") }}' + queryString, 'charts');
        fetchChartHRGA1('{{ route("adminsakittotal.chart.data") }}' + queryString, 'chartstotal');
        fetchChartHRGA1('{{ route("adminizin.chart.data") }}' + queryString, 'chartizin');
        fetchChartHRGA1('{{ route("adminizintotal.chart.data") }}' + queryString, 'chartizintotal');
        fetchChartHRGA1('{{ route("admincuti.chart.data") }}' + queryString, 'chartcuti');
        fetchChartHRGA1('{{ route("admincutitotal.chart.data") }}' + queryString, 'chartcutitotal');
        fetchChartHRGA1('{{ route("adminterlambat.chart.data") }}' + queryString, 'chartterlambat');
        fetchChartHRGA1('{{ route("adminterlambattotal.chart.data") }}' + queryString, 'chartterlambattotal');

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
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 50 // Tambahkan padding di atas agar angka tidak terpotong
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
                                    var textY = bar.y - 10; // Beri jarak lebih jauh agar angka tidak terpotong
                                    if (textY < 20) textY = 20; // Pastikan angka tidak keluar area chart
                                    ctx.fillStyle = 'black'; // Warna teks
                                    ctx.font = 'bold 15px sans-serif'; // Ukuran teks
                                    ctx.textAlign = 'center';
                                    ctx.fillText('Rp ' + value.toLocaleString(), bar.x, textY); // Tampilkan di atas bar
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
                    layout: {
                        padding: {
                            top: 50 // Tambahkan padding di atas agar angka tidak terpotong
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
                                    var textY = bar.y - 10; // Beri jarak lebih jauh agar angka tidak terpotong
                                    if (textY < 20) textY = 20; // Pastikan angka tidak keluar area chart
                                    ctx.fillStyle = 'black'; // Warna teks
                                    ctx.font = 'bold 15px sans-serif'; // Ukuran teks
                                    ctx.textAlign = 'center';
                                    ctx.fillText(value.toLocaleString(), bar.x, textY);
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
    function fetchChartPaket(url, canvasId, title) {
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
                    layout: {
                        padding: {
                            top: 50 // Tambahkan padding di atas agar angka tidak terpotong
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
                                    var textY = bar.y - 10; // Beri jarak lebih jauh agar angka tidak terpotong
                                    if (textY < 20) textY = 20; // Pastikan angka tidak keluar area chart
                                    ctx.fillStyle = 'black'; // Warna teks
                                    ctx.font = 'bold 15px sans-serif'; // Ukuran teks
                                    ctx.textAlign = 'center';
                                    ctx.fillText(value + ' Paket'.toLocaleString(), bar.x, textY);
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
    function fetchChartHRGA1(url, canvasId, title) {
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
                    layout: {
                        padding: {
                            top: 50 // Tambahkan padding di atas agar angka tidak terpotong
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
                                    var textY = bar.y - 10; // Beri jarak lebih jauh agar angka tidak terpotong
                                    if (textY < 20) textY = 20; // Pastikan angka tidak keluar area chart
                                    ctx.fillStyle = 'black'; // Warna teks
                                    ctx.font = 'bold 15px sans-serif'; // Ukuran teks
                                    ctx.textAlign = 'center';
                                    ctx.fillText(value + ' Kali'.toLocaleString(), bar.x, textY);
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
    function fetchChartHRGA1(url, canvasId, title) {
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
                    layout: {
                        padding: {
                            top: 50 // Tambahkan padding di atas agar angka tidak terpotong
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
                                    var textY = bar.y - 10; // Beri jarak lebih jauh agar angka tidak terpotong
                                    if (textY < 20) textY = 20; // Pastikan angka tidak keluar area chart
                                    ctx.fillStyle = 'black'; // Warna teks
                                    ctx.font = 'bold 15px sans-serif'; // Ukuran teks
                                    ctx.textAlign = 'center';
                                    ctx.fillText(value + ' Hari'.toLocaleString(), bar.x, textY);
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
    function decodeEntities(encodedString) {
        let textarea = document.createElement("textarea");
        textarea.innerHTML = encodedString;
        return textarea.value;
    }
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
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.pekerjaan}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.kondisi_bulanlalu}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.kondisi_bulanini}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.update}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.rencana_implementasi}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.keterangan}</div>
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    }
                },
                error: function(xhr, status, error) {
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

    $(document).ready(function() {
    function decodeEntities(encodedString) {
        let textarea = document.createElement("textarea");
        textarea.innerHTML = encodedString;
        return textarea.value;
    }

    function formatTanggal(dateString) {
        const [tahun, bulan, hari] = dateString.split('-');
        const namaBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ][parseInt(bulan, 10) - 1]; // Konversi bulan ke indeks array

        return `${parseInt(hari, 10)} ${namaBulan} ${tahun}`;
    }

    function fetchLaporaniJASA(search = '', start_month = '', end_month = '') {
        $.ajax({
            url: "{{ route('laporanijasa.index') }}",
            type: "GET",
            data: { search: search, start_month: start_month, end_month: end_month },
            dataType: "json",
            success: function(response) {
                let tableBody = $("#adminijasa tbody");
                tableBody.empty(); // Kosongkan tabel sebelum menambahkan data baru

                if (response.laporanijasas.data.length === 0) {
                    tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
                } else {
                    response.laporanijasas.data.forEach(function(item) {
                        let formattedTanggal = formatTanggal(item.tanggal);
                        let formattedResolveTanggal = formatTanggal(item.resolve_tanggal);

                        let row = `
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">${item.jam}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${decodeEntities(item.permasalahan)}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${decodeEntities(item.impact)}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${decodeEntities(item.troubleshooting)}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">${formattedResolveTanggal}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">${item.resolve_jam}</td>
                        </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            },
            error: function(xhr, status, error) {
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanLabaRugi(searchValue);
        });
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanRasio(searchValue);
        });
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanPPn(searchValue);
        });
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanTaxPlanning(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanTaxPlanning(startMonth, endMonth);
        });
        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });

    $(document).ready(function() {
    function decodeEntities(encodedString) {
        let textarea = document.createElement("textarea");
        textarea.innerHTML = encodedString;
        return textarea.value;
    }

    function fetchLaporanSPI(search = '', start_month = '', end_month = '') {
    $.ajax({
        url: "{{ route('laporanspi.index') }}",
        type: "GET",
        data: {
            search: search, start_month: start_month, end_month: end_month 
        },
        dataType: "json",
        success: function(response) {
            let tableBody = $("#adminspi tbody");
            tableBody.empty();

            if (response.laporanspis.data.length === 0) {
                tableBody.append(`<tr><td colspan="7" class="text-center p-4">Data tidak ditemukan</td></tr>`);
            } else {
                response.laporanspis.data.forEach(function(item) {
                    const [tahun, bulan, hari] = item.tanggal.split('-');
                    const namaBulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ][parseInt(bulan, 10) - 1];
                    const formattedTanggal = `${parseInt(hari, 10)} ${namaBulan} ${tahun}`;

                    // Don't use decodeEntities for HTML content
                    // Use the raw HTML directly with proper styling
                    let row = `
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">${formattedTanggal}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content">${item.aspek}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${item.masalah}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${item.solusi}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="ck-content text-justify">${item.implementasi}</div>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data:", error);
        }
    });
}
    fetchLaporanSPI();

    $("#dateFilterForm").on("submit", function(e) {
        e.preventDefault();
        let searchValue = $("#searchInput").val().trim();
        fetchLaporanSPI(searchValue);
    });
    $("#monthFilterForm").on("submit", function(e) {
        e.preventDefault();
        let startMonth = $("#startMonth").val();
        let endMonth = $("#endMonth").val();
        fetchLaporanSPI( startMonth, endMonth);
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
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.aspek}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.masalah}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.solusi}</div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.implementasi}</div>
                                    </td>
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanSPITI(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanSPITI(startMonth, endMonth);
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporaniJASAGambar(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporaniJASAGambar(startMonth, endMonth);
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanTiktok(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanTiktok(startMonth, endMonth);
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanInstagram(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanInstagram(startMonth, endMonth);
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
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="ck-content text-justify">${item.kendala}</div>
                                    </td>                                
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
        $("#dateFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let searchValue = $("#searchInput").val().trim();
            fetchLaporanBizdevGambar(searchValue);
        });
        $("#monthFilterForm").on("submit", function(e) {
            e.preventDefault(); // Mencegah form melakukan reload halaman
            let startMonth = $("#startMonth").val();
            let endMonth = $("#endMonth").val();
            fetchLaporanBizdevGambar( startMonth, endMonth);
        });
        // Event untuk menutup modal saat klik di luar gambar
        $("#imageModal").on("click", function(e) {
            if (!$(e.target).closest("#modalImage").length) {
                $(this).fadeOut();
            }
        });
    });


//export fking all

// // Function to toggle the export modal visibility
// function toggleExportModal() {
//     const modal = document.getElementById('exportModal');
//     modal.classList.toggle('hidden');
// }

// // Initialize export functionality
// document.addEventListener('DOMContentLoaded', function() {
//     // Get export button and modal elements
//     const exportFloatingButton = document.getElementById('exportFloatingButton');
//     const cancelExportBtn = document.getElementById('cancelExportBtn');
//     const confirmExportBtn = document.getElementById('confirmExportBtn');
//     const exportModal = document.getElementById('exportModal');

//     // Show modal when export button is clicked
//     if (exportFloatingButton) {
//         exportFloatingButton.addEventListener('click', toggleExportModal);
//     }

//     // Hide modal when cancel button is clicked
//     if (cancelExportBtn) {
//         cancelExportBtn.addEventListener('click', toggleExportModal);
//     }

//     // Export PDFs when confirm button is clicked
//     if (confirmExportBtn) {
//         confirmExportBtn.addEventListener('click', function() {
//             // First hide the modal
//             toggleExportModal();
//             // Show loading indicator
//             Swal.fire({
//                 title: 'Mengekspor PDF...',
//                 text: 'Mohon tunggu sebentar',
//                 allowOutsideClick: false,
//                 didOpen: () => {
//                     Swal.showLoading();
//                 }
//             });
            
//             // Get current page data
//             const currentUrl = window.location.pathname;
            
//             // Determine which export function to call based on current URL
//             if (currentUrl.includes('rekap-penjualan-perusahaan')) {
//             exportRekapPenjualanPerusahaan('/exports/rekap-penjualan-perusahaan');
//             } else if (currentUrl.includes('rekap-penjualan')) {
//             exportRekapPenjualan('/exports/rekap-penjualan');
//             } else {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Ekspor Gagal',
//                     text: 'Halaman ini tidak mendukung ekspor PDF',
//                 });
//             }
//         });
//     }
// });

// // Function to export Rekap Penjualan PDF
// function exportRekapPenjualan() {
//     // Get the table data
//     const table = document.querySelector('.dataTable');
//     if (!table) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Ekspor Gagal',
//             text: 'Data tabel tidak ditemukan',
//         });
//         return;
//     }
    
//     // Generate table HTML for PDF
//     let tableHTML = '';
//     const rows = table.querySelectorAll('tbody tr');
//     rows.forEach(row => {
//         const date = row.querySelector('td:nth-child(1)').textContent.trim();
//         const total = row.querySelector('td:nth-child(2)').textContent.trim();
//         tableHTML += `<tr>
//             <td style='border: 1px solid #000; padding: 2px;'>${date}</td>
//             <td style='border: 1px solid #000; padding: 2px;'>${total}</td>
//         </tr>`;
//     });
    
//     // Get chart as base64 image
//     const chartContainer = document.getElementById('chartContainer');
//     if (!chartContainer) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Ekspor Gagal',
//             text: 'Data grafik tidak ditemukan',
//         });
//         return;
//     }
    
//     // Use html2canvas to convert the chart to an image
//     html2canvas(chartContainer).then(canvas => {
//         const chartBase64 = canvas.toDataURL('image/png');
        
//         // Send data to server for PDF generation
//         axios.post('/exports/export-rekap-penjualan-pdf', {
//             table: tableHTML,
//             chart: chartBase64
//         })
//         .then(response => {
//             // Create a blob from the PDF data
//             const blob = new Blob([response.data], { type: 'application/pdf' });
//             const url = window.URL.createObjectURL(blob);
            
//             // Create a link to download the PDF
//             const a = document.createElement('a');
//             a.href = url;
//             a.download = 'laporan_rekap_penjualan.pdf';
//             document.body.appendChild(a);
//             a.click();
//             window.URL.revokeObjectURL(url);
            
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Ekspor Berhasil',
//                 text: 'PDF berhasil diunduh',
//             });
//         })
//         .catch(error => {
//             console.error('Error exporting PDF:', error);
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Ekspor Gagal',
//                 text: 'Terjadi kesalahan saat mengekspor PDF',
//             });
//         });
//     });
// }

// // Function to export Rekap Penjualan Perusahaan PDF
// function exportRekapPenjualanPerusahaan() {
//     // Get the table data
//     const table = document.querySelector('.dataTable');
//     if (!table) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Ekspor Gagal',
//             text: 'Data tabel tidak ditemukan',
//         });
//         return;
//     }
    
//     // Generate table HTML for PDF
//     let tableHTML = '';
//     const rows = table.querySelectorAll('tbody tr');
//     rows.forEach(row => {
//         const date = row.querySelector('td:nth-child(1)').textContent.trim();
//         const company = row.querySelector('td:nth-child(2)').textContent.trim();
//         const total = row.querySelector('td:nth-child(3)').textContent.trim();
//         tableHTML += `<tr>
//             <td style='border: 1px solid #000; padding: 2px;'>${date}</td>
//             <td style='border: 1px solid #000; padding: 2px;'>${company}</td>
//             <td style='border: 1px solid #000; padding: 2px;'>${total}</td>
//         </tr>`;
//     });
    
//     // Get chart as base64 image
//     const chartContainer = document.getElementById('chartContainer');
//     if (!chartContainer) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Ekspor Gagal',
//             text: 'Data grafik tidak ditemukan',
//         });
//         return;
//     }
    
//     // Use html2canvas to convert the chart to an image
//     html2canvas(chartContainer).then(canvas => {
//         const chartBase64 = canvas.toDataURL('image/png');
        
//         // Send data to server for PDF generation
//         axios.post('/exports/export-rekap-penjualan-perusahaan-pdf', {
//             table: tableHTML,
//             chart: chartBase64
//         })
//         .then(response => {
//             // Create a blob from the PDF data
//             const blob = new Blob([response.data], { type: 'application/pdf' });
//             const url = window.URL.createObjectURL(blob);
            
//             // Create a link to download the PDF
//             const a = document.createElement('a');
//             a.href = url;
//             a.download = 'laporan_rekap_penjualan_perusahaan.pdf';
//             document.body.appendChild(a);
//             a.click();
//             window.URL.revokeObjectURL(url);
            
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Ekspor Berhasil',
//                 text: 'PDF berhasil diunduh',
//             });
//         })
//         .catch(error => {
//             console.error('Error exporting PDF:', error);
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Ekspor Gagal',
//                 text: 'Terjadi kesalahan saat mengekspor PDF',
//             });
//         });
//     });
// }


// document.getElementById('exportFloatingButton').addEventListener('click', async function () {
//     const pathname = window.location.pathname;
//     let endpoint = '';
//     let filename = '';

//     // Tentukan endpoint berdasarkan halaman
//     if (pathname.includes('rekap-penjualan-perusahaan')) {
//         endpoint = '/export/rekap-penjualan-perusahaan/pdf';
//         filename = 'laporan_rekap_penjualan_perusahaan.pdf';
//     } else if (pathname.includes('rekap-penjualan')) {
//         endpoint = '/export/rekap-penjualan/pdf';
//         filename = 'laporan_rekap_penjualan.pdf';
//     } else {
//         alert('Halaman ini tidak mendukung ekspor PDF.');
//         return;
//     }

//     // Ambil konten tabel
//     const table = document.querySelector('#rekapTable');
//     if (!table) {
//         alert('Tabel tidak ditemukan.');
//         return;
//     }

//     const tableBodyRows = table.querySelectorAll('tbody tr');
//     if (tableBodyRows.length === 0) {
//         alert('Data tabel kosong.');
//         return;
//     }

//     let tableHTML = '';
//     tableBodyRows.forEach(row => {
//         tableHTML += `<tr>${row.innerHTML}</tr>`;
//     });

//     // Ambil elemen canvas chart
//     const canvas = document.querySelector('canvas');
//     if (!canvas) {
//         alert('Grafik tidak ditemukan.');
//         return;
//     }

//     const chartBase64 = canvas.toDataURL('image/png');

//     try {
//         const response = await fetch(endpoint, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//             },
//             body: JSON.stringify({
//                 table: tableHTML,
//                 chart: chartBase64,
//             })
//         });

//         if (!response.ok) {
//             const errorData = await response.json();
//             throw new Error(errorData.message || 'Gagal mengekspor PDF.');
//         }

//         const blob = await response.blob();
//         const url = URL.createObjectURL(blob);

//         const link = document.createElement('a');
//         link.href = url;
//         link.download = filename;
//         document.body.appendChild(link);
//         link.click();
//         document.body.removeChild(link);
//         URL.revokeObjectURL(url);
//     } catch (error) {
//         alert('Terjadi kesalahan saat mengekspor PDF: ' + error.message);
//         console.error(error);
//     }
// });


document.addEventListener('DOMContentLoaded', function () {
    const exportButton = document.getElementById('exportFloatingButton');

    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const tableBody = document.querySelector('#rekapTable tbody');
            const tableHTML = tableBody ? tableBody.innerHTML.trim() : '';

            const chartCanvas = document.getElementById('rekapChart');
            const chartBase64 = chartCanvas ? chartCanvas.toDataURL('image/png') : '';

            if (!tableHTML || !chartBase64) {
                alert('Data tabel atau grafik tidak tersedia.');
                return;
            }

            fetch('/exports/rekap-penjualan.pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    table: tableHTML,
                    chart: chartBase64
                })
            })
                .then(response => {
                    if (!response.ok) throw new Error("Gagal mengekspor PDF");
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'laporan_rekap_penjualan.pdf';
                    link.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert('Gagal mengekspor PDF.');
                });
        });
    }
});


</script>

