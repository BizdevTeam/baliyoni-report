@if (Auth::check())
<aside id="sidebar" class="w-64 transition-all duration-300 bg-white border-r border-gray-200 shadow-lg overflow-y-auto h-screen fixed top-0 left-0 z-20 flex flex-col">
    <!-- Logo Section -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto">
            <img src="{{ asset('images/baliyoni.png') }}" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-10 h-auto hidden">
            <img src="{{ asset('images/BYS_LOGO.png') }}" class="w-full" alt="Logo Mini">
        </div>
    </div>

    <!-- User Info and Navigation -->
    <div class="user-info mb-4">
        @if (Auth::user()->role == 'superadmin')

        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="/admin" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset ("images/homepage.svg") }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>
                <!-- Marketing -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md transition" aria-controls="dropdown-marketing" aria-expanded="false">
                        <img src="{{ asset("images/marketing.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Marketing</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 transition-transform"></i>
                    </button>
                    <ul id="dropdown-marketing" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route("rekappenjualan.index") }}" class="flex items-center px-3 py-2 text-gray-700  transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Penjualan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("rekappenjualanperusahaan.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Penjualan Perusahaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanpaketadministrasi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Paket Administrasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("statuspaket.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Status Paket </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanperinstansi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPerinstansi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Per Instansi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("perusahaan.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPerinstansi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Tambah Data Perusahaan</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Procurement -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-procurement" aria-expanded="false">
                        <img src="{{ asset("images/procurement.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Procurement</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-procurement" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route("laporanholding.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Pembelian (Holding)</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanstok.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Stok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanoutlet.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Pembelian Outlet</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporannegosiasi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Negosiasi</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Support -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-support" aria-expanded="false">
                        <img src="{{ asset("images/support.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Support</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-support" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('rekappendapatanservisasp.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Pendapatan Servis ASP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rekappiutangservisasp.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Piutang Servis ASP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporandetrans.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Laporan Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Accounting -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-accounting" aria-expanded="false">
                        <img src="{{ asset("images/accounting.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-accounting" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('labarugi.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Laba Rugi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('neraca.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Neraca</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rasio.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Rasio</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("khps.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/kashutangpiutang.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Kas Hutang Piutang Stok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aruskas.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/aruskas.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Arus Kas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route('laporanppn.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ppnlebihbayar.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan PPn</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('taxplaning.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/taxplanning.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Tax Planning </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- It -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-IT" aria-expanded="false">
                        <img src="{{ asset("images/it.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">IT</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-IT" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('multimediainstagram.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Multimedia IG</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tiktok.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Multimedia Tiktok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanbizdev.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Bizdev</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanbizdevgambar.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Bizdev Gambar</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- HRGA -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-hrga" aria-expanded="false">
                        <img src="{{ asset("images/hrga.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">HRGA</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-hrga" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('laporanptbos.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/ptbos.svg") }}" class="w-5 h-5">
                                <span class="ml-2">PT.BOS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanijasa.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ijasa.svg") }}" class="w-5 h-5">
                                <span class="ml-2">iJASA</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("ijasagambar.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ijasa.svg") }}" class="w-5 h-5">
                                <span class="ml-2">iJASA Gambar</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporansakit.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporansakit.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Sakit</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanizin.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporanizin.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Izin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporancuti.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporancuti.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Cuti</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanterlambat.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporanterlambat.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Terlambat</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- SPI -->
                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-spi" aria-expanded="false">
                        <img src="{{ asset("images/spi.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">SPI</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-spi" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('laporanspi.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan SPI Operasional</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanspiti.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan SPI IT</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="{{ route("evaluasi.index") }}" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('icon/evaluasi.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Evaluasi Kinerja</span>
                        </a>
                    </li>
                </div>
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="{{ route("questions.index") }}" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/ask.svg') }}" class="w-5 h-5">
                            <span class="menu-label">FAQ</span>
                        </a>
                    </li>
                </div>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'marketing')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="/admin" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md transition" aria-controls="dropdown-marketing" aria-expanded="false">
                        <img src="{{ asset("images/marketing.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Marketing</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 transition-transform"></i>
                    </button>
                    <ul id="dropdown-marketing" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route("rekappenjualan.index") }}" class="flex items-center px-3 py-2 text-gray-700  transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Penjualan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("rekappenjualanperusahaan.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Penjualan Perusahaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanpaketadministrasi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Paket Administrasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("statuspaket.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Status Paket </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanperinstansi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPerinstansi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Per Instansi</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'procurement')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="/admin" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-procurement" aria-expanded="false">
                        <img src="{{ asset("images/procurement.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Procurement</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-procurement" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route("laporanholding.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Pembelian (Holding)</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanstok.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Stok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanoutlet.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Pembelian Outlet</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporannegosiasi.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Negosiasi</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'it')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-IT" aria-expanded="false">
                        <img src="{{ asset("images/it.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">IT</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-IT" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('multimediainstagram.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Multimedia IG</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tiktok.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Multimedia Tiktok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanbizdev.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Bizdev</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanbizdevgambar.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Bizdev Gambar</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'accounting')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-accounting" aria-expanded="false">
                        <img src="{{ asset("images/accounting.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-accounting" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('labarugi.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Laba Rugi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('neraca.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Neraca</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rasio.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Rasio</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("khps.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/kashutangpiutang.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Kas Hutang Piutang Stok</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("aruskas.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/aruskas.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Arus Kas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route('laporanppn.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ppnlebihbayar.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan PPn</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('taxplaning.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/taxplanning.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Tax Planning </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'hrga')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-hrga" aria-expanded="false">
                        <img src="{{ asset("images/hrga.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">HRGA</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-hrga" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('laporanptbos.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/ptbos.svg") }}" class="w-5 h-5">
                                <span class="ml-2">PT.BOS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanijasa.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ijasa.svg") }}" class="w-5 h-5">
                                <span class="ml-2">iJASA</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("ijasagambar.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/ijasa.svg") }}" class="w-5 h-5">
                                <span class="ml-2">iJASA Gambar</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporansakit.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporansakit.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Sakit</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanizin.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporanizin.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Izin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporancuti.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporancuti.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Cuti</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporanterlambat.index") }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/laporanterlambat.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan Terlambat</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'spi')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-spi" aria-expanded="false">
                        <img src="{{ asset("images/spi.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">SPI</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-spi" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('laporanspi.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan SPI Operasional</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporanspiti.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Laporan SPI IT</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>

        @elseif(Auth::user()->role == 'support')
        <nav class="mt-4">
            <ul class="flex flex-col space-y-3">
                <div class="home">
                    <li class="group hover:text-white menu-item">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                            <img src="{{ asset('images/homepage.svg') }}" class="w-5 h-5">
                            <span class="menu-label">Homepage</span>
                        </a>
                    </li>
                </div>

                <li class="relative">
                    <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-support" aria-expanded="false">
                        <img src="{{ asset("images/support.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Support</span>
                        <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                    </button>
                    <ul id="dropdown-support" class="hidden py-2 pl-8 space-y-2">
                        <li>
                            <a href="{{ route('rekappendapatanservisasp.index') }}" class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Pendapatan Servis ASP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rekappiutangservisasp.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Piutang Servis ASP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route("laporandetrans.index") }}" class="flex items-center px-3 py-2 text-gray-700 rounded-md . transition">
                                <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                                <span class="ml-2">Rekap Laporan Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="relative mb-24">
                    <a href="{{ route('logout') }}">
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition" aria-controls="dropdown-logout" aria-expanded="false">
                            <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5">
                            <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                        </button>
                    </a>
                </li>
            </ul>
        </nav>


        @endif
    </div>
</aside>
@endif
