<aside id="sidebar"
    class="w-64 transition-all duration-300 bg-white border-r border-gray-200 shadow-lg h-screen fixed top-0 left-0 z-20 flex flex-col overflow-y-auto overflow-x-hidden">
    <!-- Sidebar Content -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto">
            <img src="{{ asset("images/baliyoni.png") }}" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-10 h-auto hidden">
            <img src="{{ asset("images/BYS_LOGO.png") }}" class="w-full" alt="Logo Full">
        </div>
    </div>

    @if(Auth::check())
                    <span class="text-white font-medium">{{ Auth::user()->name }}</span>
            
                    <!-- Conditionally render the role-specific navbar items -->
                    @if(Auth::user()->role == 'superadmin')
                        <!-- SuperAdmin navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to superadmin -->
                    @elseif(Auth::user()->role == 'marketing')
                        <!-- Marketing navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to marketing -->
                    @elseif(Auth::user()->role == 'it')
                        <!-- IT navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to IT -->
                    @elseif(Auth::user()->role == 'procurement')
                        <!-- Procurement navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to procurement -->
                    @elseif(Auth::user()->role == 'accounting')
                        <!-- Accounting navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to accounting -->
                    @elseif(Auth::user()->role == 'support')
                        <!-- Support navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to support -->
                    @elseif(Auth::user()->role == 'hrga')
                        <!-- HRGA navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to HRGA -->
                    @elseif(Auth::user()->role == 'spi')
                        <!-- SPI navbar content -->
                        <span class="text-white"></span>
                        <!-- Add other items specific to SPI -->
                    @endif
                @else
                    <span class="text-white font-medium">Guest</span>
                @endif
                
    <nav class="mt-4">
        <ul class="flex flex-col space-y-3">
            <div class="home">
            <li class="group hover:text-white menu-item">
                <a href="/admin"
                    class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                    <img src="{{ asset ("images/homepage.svg") }}" class="w-5 h-5">
                    <span class="menu-label">Homepage</span>
                </a>
            </li>
            </div>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md    transition"
                    aria-controls="dropdown-marketing" aria-expanded="false">
                    <img src="{{ asset("images/marketing.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Marketing</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 transition-transform"></i>
                </button>
                <ul id="dropdown-marketing" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route("marketings.rekappenjualan") }}"
                            class="flex items-center px-3 py-2 text-gray-700  transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("marketings.rekappenjualanperusahaan") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan Perusahaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("marketings.laporanpaketadministrasi") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Paket Administrasi</span>
                        </a>
                    </li>
                    <li>
                       <a href="{{ route("marketings.laporanstatuspaket") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Status Paket </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("marketings.laporanperinstansi") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanPerinstansi.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Per Instansi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-procurement" aria-expanded="false">
                    <img src="{{ asset("images/procurement.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Procurement</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-procurement" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route("procurements.laporanpembelianholding") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian (Holding)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("procurements.laporanstok") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Stok</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("procurements.laporanpembelianoutlet") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian Outlet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("procurements.laporannegosiasi") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanStatusPaket.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Negosiasi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-IT" aria-expanded="false">
                    <img src="{{ asset("images/it.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">IT</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-IT" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route('instagram.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia IG</span>
                        </a>
                    </li>
                    <li>    
                        <a href="{{ route('tiktok.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia Tiktok</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bizdevbulanan.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Bizdev</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-accounting" aria-expanded="false">
                    <img src="{{ asset("images/accounting.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-accounting" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route('labarugi.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Laba Rugi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('neraca.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Neraca</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rasio.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/LaporanPaketAdministrasi.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Rasio</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("accounting.kashutangpiutangstok") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/kashutangpiutang.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Kas, Hutang, Piutang, Stok</span>
                        </a>
                    </li>
                    <li>
                    <a href="{{ route("accounting.aruskas") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/aruskas.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Arus Kas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ Route('laporanppn.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/ppnlebihbayar.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan PPn</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('taxplaning.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/taxplanning.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Tax Planning vs Penjualan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-hrga" aria-expanded="false">
                    <img src="{{ asset("images/hrga.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">HRGA</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-hrga" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route("hrga.laporanptbos") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/ptbos.svg") }}" class="w-5 h-5">
                            <span class="ml-2">PT.BOS</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("hrga.laporanijasa") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/ijasa.svg") }}" class="w-5 h-5">
                            <span class="ml-2">iJASA</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("hrga.laporansakit") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/laporansakit.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Sakit</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("hrga.laporanizin") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/laporanizin.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Izin</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("hrga.laporancuti") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/laporancuti.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Cuti</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("hrga.laporanterlambat") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/laporanterlambat.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan Terlambat</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-spi" aria-expanded="false">
                    <img src="{{ asset("images/spi.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">SPI</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-spi" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route('laporanspi.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan SPI</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('laporanspiti.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Laporan SPI IT</span>
                        </a>
                    </li>
            </li>
        </ul>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-support" aria-expanded="false">
                    <img src="{{ asset("images/support.svg") }}" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">SUPPORT</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-support" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route("supports.rekappendapatanservisasp") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Rekap Pendapatan Servis ASP</span>
                        </a>
                    </li>
                        <a href="{{ route("supports.rekappiutangservisasp") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">Rekap Piutang Servis ASP</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route("supports.laporansamitra") }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="{{ asset("icon/RekapPenjualan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">PT. Saguna Mitra Transindo</span>
                        </a>
                    </li>
                        <a href="{{ route("supports.laporandetrans") }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="{{ asset("icon/RekapPenjualanPerusahaan.svg") }}" class="w-5 h-5">
                            <span class="ml-2">PT. Detran Jelajah Nusantara</span>
                        </a>
                    </li>
                </ul>
            <li class="relative">
                <form method="post" action="{{ route("logout") }}">
                @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                        aria-controls="dropdown-logout" aria-expanded="false">
                        <img src="{{ asset("images/logout.svg") }}" class="w-5 h-5">
                        <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                    </button>
                </form>
            </li>
           
        <!-- Tambahkan menu lainnya di sini -->
        </ul>
    </nav>
</aside>

