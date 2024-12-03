<aside id="sidebar"
    class="w-64 transition-all duration-300 bg-white border-r border-gray-200 shadow-lg overflow-y-auto h-screen fixed top-0 left-0 z-20 flex flex-col">
    <!-- Sidebar Content -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto">
            <img src="images/baliyoni.png" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-12 h-auto hidden">
            <img src="images/baliyoni- mini.png" class="w-full" alt="Logo Mini">
        </div>
    </div>
    <nav class="mt-4">
        <ul class="flex flex-col space-y-3">
            <div class="home">
            <li class="group">
                <a href="#"
                    class="flex items-center space-x-3 px-4 py-2 rounded-md text-gray-700  transition">
                    <img src="images/homepage.svg" class="w-5 h-5">
                    <span class="menu-label">Homepage</span>
                </a>
            </li>
            </div>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md    transition"
                    aria-controls="dropdown-marketing" aria-expanded="false">
                    <img src="images/marketing.svg" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Marketing</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 transition-transform"></i>
                </button>
                <ul id="dropdown-marketing" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="/marketings/rekappenjualan"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan</span>
                        </a>
                    </li>
                    <li>
                        <a href="marketings/rekappenjualanperusahaan"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan Perusahaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="marketings/laporanpaketadministrasi"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanPaketAdministrasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Paket Administrasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="marketings/statuspaket"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanStatusPaket.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Status Paket </span>
                        </a>
                    </li>
                    <li>
                        <a href="marketings/laporanperinstansi"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanPerinstansi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Per Instansi</span>
                        </a>
                    </li> <!-- Tambahkan submenu lainnya di sini -->
                </ul>

            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-procurement" aria-expanded="false">
                    <img src="images/procurement.svg" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Procurement</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-procurement" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="procurements/laporanpembelianholding"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian (Holding)</span>
                        </a>
                    </li>
                    <li>
                        <a href="procurements/laporanstok"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Stok</span>
                        </a>
                    </li>
                    <li>
                        <a href="procurements/laporanpembelianoutlet"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanPaketAdministrasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian Outlet</span>
                        </a>
                    </li>
                    <li>
                        <a href="procurements/laporannegosiasi"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanStatusPaket.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Negosiasi</span>
                        </a>
                    </li><!-- Tambahkan submenu lainnya di sini -->
                </ul>

            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-IT" aria-expanded="false">
                    <img src="images/it.svg" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">IT</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-IT" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="{{ route('instagram.index') }}"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia IG</span>
                        </a>
                    </li>
                    <li>    
                        <a href="{{ route('tiktok.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia Tiktok</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bizdevbulanan.index') }}"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanPaketAdministrasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Bizdev</span>
                        </a>
                    </li>

                </ul>

            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-accounting" aria-expanded="false">
                    <img src="images/accounting.svg" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-accounting" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Laba Rugi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Neraca</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/LaporanPaketAdministrasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Rasio</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/kashutangpiutang.svg" class="w-5 h-5">
                            <span class="ml-2">Kas,Hutang,piutang</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/aruskas.svg" class="w-5 h-5">
                            <span class="ml-2">Arus Kas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/ppnlebihbayar.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan PPn</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/taxplanning.svg" class="w-5 h-5">
                            <span class="ml-2">Tax Planning vs Penjualan</span>
                        </a>
                    </li>

                </ul>

            </li>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                    aria-controls="dropdown-spi" aria-expanded="false">
                    <img src="images/spi.svg" class="w-5 h-5">
                    <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                    <i class="fas fa-chevron-down ml-auto text-gray-400 group-hover:text-gray-700 transition-transform"></i>
                </button>
                <ul id="dropdown-spi" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center  px-3 py-2 text-gray-700 rounded-md . transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan
                                SPI</span>
                        </a>
                    </li>
                    <>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-gray-700 rounded-lg . transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan
                                SPI-IT</span>
                        </a>
            </li>
        </ul>
        <li class="relative">
            <button type="button"
                class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md . . transition"
                aria-controls="dropdown-logout" aria-expanded="false">
                <img src="images/logout.svg" class="w-5 h-5">
                <span class="menu-label flex-1 ml-3 text-left">Logout</span>
               
            </button>



        </li>


        <!-- Tambahkan menu lainnya di sini -->
        </ul>
    </nav>
</aside>