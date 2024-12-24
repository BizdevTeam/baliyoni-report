<aside id="sidebar"
    class="w-64 transition-all duration-300 bg-white border-r border-gray-200 shadow-lg overflow-y-auto h-screen fixed top-0 left-0 z-20 flex flex-col">
    <!-- Sidebar Content -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto">
            <img src="{{ asset("images/baliyoni.png") }}" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-10 h-auto hidden">
            <img src="{{ asset("images/BYS_LOGO.png") }}" class="w-full" alt="Logo Full">
        </div>
    </div>
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
        </ul>
    </nav>
</aside>

