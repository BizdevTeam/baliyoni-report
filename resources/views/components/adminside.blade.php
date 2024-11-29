<aside id="sidebar"
    class="w-72 transition-all duration-300 bg-gray-50 border-r border-gray-200 shadow-lg overflow-y-auto h-screen fixed top-0 left-0 z-20 flex flex-col"
    data-minimized="false">
    <!-- Sidebar Content -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto">
            <img src="images/baliyoni.png" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-12 h-auto hidden">
            <img src="images/baliyoni-mini.png" class="w-full" alt="Logo Mini">
        </div>
    </div>
    <nav class="mt-4">
        <ul class="flex flex-col space-y-2">
            <li class="group">
                <a href="#"
                    class="flex items-center space-x-2 px-3 py-2 rounded-md text-gray-700 hover:bg-red-600 hover:text-white transition">
                    <img src="images/homepage.svg">
                    <span class="menu-label">Homepage</span>
                </a>
            </li>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-marketing" aria-expanded="false">
                    <img src="images/marketing.svg">
                    <span class="menu-label flex-1 ml-3 text-left">Marketing</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-marketing" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="/marketings/rekappenjualan"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/RekapPenjualan.svg" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/RekapPenjualanPerusahaan.svg" class="w-5 h-5">
                            <span class="ml-2">Rekap Penjualan Perusahaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="/marketings/laporanpaketadministrasi"
                            class="flex items-center block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                            <img src="icon/LaporanPaketAdministrasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Paket Administrasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/LaporanStatusPaket.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Status Paket </span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/LaporanPerinstansi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Per Instansi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-procurement" aria-expanded="false">
                    <img src="images/procurement.svg">
                    <span class="menu-label flex-1 ml-3 text-left">Procurement</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-procurement" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanpembelian.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian (Holding) </span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanstok.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Stok </span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanpembelianoutlet.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Pembelian Oulet </span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporannegosiasi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Negosiasi </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-support" aria-expanded="false">
                    <img src="images/support.svg">
                    <span class="menu-label flex-1 ml-3 text-left">Support</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-support" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/pendapatanservisasp.svg" class="w-5 h-5">
                            <span class="ml-2">Pendapatan Servis ASP</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/piutangservisasp.svg" class="w-5 h-5">
                            <span class="ml-2">Piutang Servis ASP</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/penggirimansamitra.svg" class="w-5 h-5">
                            <span class="ml-2">Penggiriman Samitra</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/penggirimandetran.svg" class="w-5 h-5">
                            <span class="ml-2">Penggiriman Detran</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-IT" aria-expanded="false">
                    <img src="images/it.svg">
                    <span class="menu-label flex-1 ml-3 text-left">IT</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-IT" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanmultimediaig.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia IG</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanmultimediatiktok.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Multimedia Tiktok</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanbizdev.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Bizdev</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-accounting" aria-expanded="false">
                    <img src="images/accounting.svg">
                    <span class="menu-label flex-1 ml-3 text-left">Accounting</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-accounting" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/labarugi.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Laba Rugi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/neraca.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Neraca</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/rasio.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Rasio</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/kashutangpiutang.svg" class="w-5 h-5">
                            <span class="ml-2">Kas,Hutang,piutang</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/aruskas.svg" class="w-5 h-5">
                            <span class="ml-2">Arus Kas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/ppnlebihbayar.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan PPn</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/taxplanning.svg" class="w-5 h-5">
                            <span class="ml-2">Tax Planning vs Penjualan</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-hrga" aria-expanded="false">
                    <img src="images/hrga.svg">
                    <span class="menu-label flex-1 ml-3 text-left">HRGA</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-hrga" class="hidden py-2 pl-6 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/ptbos.svg" class="w-5 h-5">
                            <span class="ml-2">PT.BOS</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/ijasa.svg" class="w-5 h-5">
                            <span class="ml-2">iJASA</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporansakit.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Sakit</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanizin.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Izin</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporancuti.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Cuti</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                            <img src="icon/laporanterlambat.svg" class="w-5 h-5">
                            <span class="ml-2">Laporan Terlambat</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <button type="button"
                    class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                    aria-controls="dropdown-spi" aria-expanded="false">
                    <img src="images/spi.svg">
                    <span class="menu-label flex-1 ml-3 text-left">SPI</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform"></i>
                </button>
                <ul id="dropdown-spi" class="hidden py-2 pl-6 space-y-2">
                        <li>
                                <a href="#"
                                    class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                                    <img src="icon/laporanspi.svg" class="w-5 h-5">
                                    <span class="ml-2">Laporan SPI</span>
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="flex items-center block px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition">
                                    <img src="icon/laporanspiit.svg" class="w-5 h-5">
                                    <span class="ml-2">Laporan SPI-IT</span>
                                </a>
                            </li>
                </ul>
            </li>
            <li class="relative"><a href="/logout">
                    <button type="button"
                        class="flex items-center w-full px-3 py-2 text-gray-700  hover:bg-red-600 hover:text-white transition"
                        aria-controls="dropdown-logout" aria-expanded="false">
                        <img src="images/logout.svg">
                        <span class="menu-label flex-1 ml-3 text-left">Logout</span>
        </ul>
    </nav>
</aside>
