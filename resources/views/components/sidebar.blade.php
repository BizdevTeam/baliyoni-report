@php
// Define the entire sidebar navigation structure in one place.
// This makes it much easier to manage and update.
$menuItems = [
    // Menu item 'Homepage'
    [
        'title' => 'Homepage',
        'route' => '/admin/app',
        'icon' => 'images/homepage.svg',
        'roles' => ['superadmin', 'marketing', 'procurement', 'it', 'accounting', 'hrga', 'spi', 'support'],
    ],
    // Dropdown 'Marketing'
    [
        'title' => 'Marketing',
        'icon' => 'images/marketing.svg',
        'roles' => ['superadmin', 'marketing'],
        'id' => 'dropdown-marketing',
        'submenu' => [
            ['title' => 'Sales Recap', 'route' => 'rekappenjualan.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'Sales Recap by Company', 'route' => 'rekappenjualanperusahaan.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
            ['title' => 'Administrative Package Report', 'route' => 'laporanpaketadministrasi.index', 'icon' => 'icon/LaporanPaketAdministrasi.svg'],
            ['title' => 'Package Status Report', 'route' => 'statuspaket.index', 'icon' => 'icon/LaporanStatusPaket.svg'],
            ['title' => 'Institution-Based Report', 'route' => 'laporanperinstansi.index', 'icon' => 'icon/LaporanPerinstansi.svg'],
            ['title' => 'Add Company Data', 'route' => 'perusahaan.index', 'icon' => 'icon/LaporanPerinstansi.svg', 'roles' => ['superadmin']],
        ],
    ],
    // Dropdown 'Procurement'
    [
        'title' => 'Procurement',
        'icon' => 'images/procurement.svg',
        'roles' => ['superadmin', 'procurement'],
        'id' => 'dropdown-procurement',
        'submenu' => [
            ['title' => 'Purchase Report (Holding)', 'route' => 'laporanholding.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'Outlet Purchase Report', 'route' => 'laporanoutlet.index', 'icon' => 'icon/LaporanPaketAdministrasi.svg'],
            ['title' => 'Stock Report', 'route' => 'laporanstok.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
            ['title' => 'Negotiation Report', 'route' => 'laporannegosiasi.index', 'icon' => 'icon/LaporanStatusPaket.svg'],
        ],
    ],
    // Dropdown 'Support'
    [
        'title' => 'Support',
        'icon' => 'images/support.svg',
        'roles' => ['superadmin', 'support'],
        'id' => 'dropdown-support',
        'submenu' => [
            ['title' => 'ASP Service Revenue Recap', 'route' => 'rekappendapatanservisasp.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'ASP Service Receivables Recap', 'route' => 'rekappiutangservisasp.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
            ['title' => 'Shipping Report Recap', 'route' => 'laporandetrans.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
        ],
    ],
    // Dropdown 'Accounting'
    [
        'title' => 'Accounting',
        'icon' => 'images/accounting.svg',
        'roles' => ['superadmin', 'accounting'],
        'id' => 'dropdown-accounting',
        'submenu' => [
            ['title' => 'Laporan Laba Rugi', 'route' => 'labarugi.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'Laporan Neraca', 'route' => 'neraca.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
            ['title' => 'Laporan Rasio', 'route' => 'rasio.index', 'icon' => 'icon/LaporanPaketAdministrasi.svg'],
            ['title' => 'Kas Hutang Piutang Stok', 'route' => 'khps.index', 'icon' => 'icon/kashutangpiutang.svg'],
            ['title' => 'Arus Kas', 'route' => 'aruskas.index', 'icon' => 'icon/aruskas.svg'],
            ['title' => 'Laporan PPn', 'route' => 'laporanppn.index', 'icon' => 'icon/ppnlebihbayar.svg'],
            ['title' => 'Tax Planning', 'route' => 'taxplaning.index', 'icon' => 'icon/taxplanning.svg'],
        ],
    ],
    // Dropdown 'IT'
    [
        'title' => 'IT',
        'icon' => 'images/it.svg',
        'roles' => ['superadmin', 'it'],
        'id' => 'dropdown-IT',
        'submenu' => [
            ['title' => 'Laporan Multimedia IG', 'route' => 'multimediainstagram.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'Laporan Multimedia Tiktok', 'route' => 'tiktok.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
            ['title' => 'Laporan Bizdev', 'route' => 'laporanbizdevgambar.index', 'icon' => 'icon/LaporanPaketAdministrasi.svg'],
        ],
    ],
    // Dropdown 'HRGA'
    [
        'title' => 'HRGA',
        'icon' => 'images/hrga.svg',
        'roles' => ['superadmin', 'hrga'],
        'id' => 'dropdown-hrga',
        'submenu' => [
            ['title' => 'PT.BOS', 'route' => 'laporanptbos.index', 'icon' => 'icon/ptbos.svg'],
            ['title' => 'iJASA', 'route' => 'laporanijasa.index', 'icon' => 'icon/ijasa.svg'],
            ['title' => 'iJASA Gambar', 'route' => 'ijasagambar.index', 'icon' => 'icon/ijasa.svg'],
            ['title' => 'Sick Leave Report', 'route' => 'laporansakitdivisi.index', 'icon' => 'icon/laporansakit.svg'],
            ['title' => 'Permission/Leave Report', 'route' => 'laporanizindivisi.index', 'icon' => 'icon/laporanizin.svg'],
            ['title' => 'Annual Leave Report', 'route' => 'laporancutidivisi.index', 'icon' => 'icon/laporancuti.svg'],
            ['title' => 'Late Arrival Report', 'route' => 'laporanterlambatdivisi.index', 'icon' => 'icon/laporanterlambat.svg'],
        ],
    ],
    // Dropdown 'SPI'
    [
        'title' => 'SPI',
        'icon' => 'images/spi.svg',
        'roles' => ['superadmin', 'spi'],
        'id' => 'dropdown-spi',
        'submenu' => [
            ['title' => 'Laporan SPI Operasional', 'route' => 'laporanspi.index', 'icon' => 'icon/RekapPenjualan.svg'],
            ['title' => 'Laporan SPI IT', 'route' => 'laporanspiti.index', 'icon' => 'icon/RekapPenjualanPerusahaan.svg'],
        ],
    ],
    // Menu item 'Evaluasi Kinerja'
    [
        'title' => 'Evaluasi Kinerja',
        'route' => 'evaluasi.index',
        'icon' => 'icon/evaluasi.svg',
        'roles' => ['superadmin'],
    ],
    // Menu item 'FAQ'
    [
        'title' => 'FAQ',
        'route' => 'questions.index',
        'icon' => 'images/ask.svg',
        'roles' => ['superadmin'],
    ],
    // Menu item 'User'
    [
        'title' => 'User',
        'route' => 'admin.users.index',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M9 15.616q-.877 0-1.496-.62t-.62-1.496t.62-1.496T9 11.384t1.496.62t.62 1.496t-.62 1.496t-1.496.62M5.616 21q-.691 0-1.153-.462T4 19.385V6.615q0-.69.463-1.152T5.616 5h1.769V2.77h1.077V5h7.154V2.77h1V5h1.769q.69 0 1.153.463T20 6.616v12.769q0 .69-.462 1.153T18.384 21zm0-1h12.769q.23 0 .423-.192t.192-.424v-8.768H5v8.769q0 .23.192.423t.423.192M5 9.615h14v-3q0-.23-.192-.423T18.384 6H5.616q-.231 0-.424.192T5 6.616zm0 0V6z"/></svg>',
        'roles' => ['superadmin'],
    ],
];
@endphp

@if (Auth::check())
{{-- Outer container for positioning --}}
<aside id="sidebar" class="w-64 h-screen fixed top-0 left-0 z-20 transition-all duration-300">
    {{-- Inner container for scrolling and styling --}}
    <div class="h-full flex flex-col bg-white border-r border-gray-200 shadow-lg overflow-y-auto no-scrollbar">

        <!-- Logo Section -->
        <div class="mt-8 mb-8 pb-3 flex justify-center flex-shrink-0">
            <div id="logo-full" class="logo w-40 h-auto">
                <p class="flex items-center justify-center text-center text-3xl font-bold text-red-600">
                    Art<span class="text-black">Work</span>
                </p>
            </div>
            <div id="logo-mini" class="logo w-10 h-auto hidden">
                <p class="flex items-center justify-center text-center text-[10px] font-bold text-red-600">
                    ArtWork
                </p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow px-2">
            <ul class="flex flex-col space-y-2">
                @foreach ($menuItems as $item)
                    {{-- Check if the current user's role is in the item's role list --}}
                    @if (in_array(Auth::user()->role, $item['roles']))
                        
                        {{-- Check if it is a dropdown menu --}}
                        @if (isset($item['submenu']))
                            <li class="relative">
                                <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md transition" aria-controls="{{ $item['id'] }}" aria-expanded="false">
                                    <img src="{{ asset($item['icon']) }}" class="w-5 h-5">
                                    <span class="menu-label flex-1 ml-3 text-left">{{ $item['title'] }}</span>
                                    <i class="fas fa-chevron-down ml-auto text-gray-400 transition-transform"></i>
                                </button>
                                <ul id="{{ $item['id'] }}" class="hidden py-2 pl-8 space-y-2">
                                    @foreach ($item['submenu'] as $subItem)
                                        {{-- Check roles for submenu item if they exist --}}
                                        @if (!isset($subItem['roles']) || in_array(Auth::user()->role, $subItem['roles']))
                                        <li>
                                            <a href="{{ route($subItem['route']) }}" class="flex items-center px-3 py-2 text-gray-700 transition">
                                                <img src="{{ asset($subItem['icon']) }}" class="w-5 h-5">
                                                <span class="ml-2">{{ $subItem['title'] }}</span>
                                            </a>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                        {{-- This is a single menu item --}}
                            <li>
                                <a href="{{ isset($item['route']) ? (Str::startsWith($item['route'], '/') ? url($item['route']) : route($item['route'])) : '#' }}" class="flex items-center space-x-3 px-4 py-2 rounded-md transition">
                                    @if (isset($item['icon_svg']))
                                        {!! $item['icon_svg'] !!}
                                    @else
                                        <img src="{{ asset($item['icon']) }}" class="w-5 h-5">
                                    @endif
                                    <span class="menu-label">{{ $item['title'] }}</span>
                                </a>
                            </li>
                        @endif

                    @endif
                @endforeach
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="mt-auto p-4 flex-shrink-0">
             <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <button type="button" class="flex items-center w-full px-4 py-2 text-gray-700 rounded-md transition hover:bg-red-500 hover:text-white">
                    <img src="{{ asset('images/logout.svg') }}" class="w-5 h-5 hover:text-white">
                    <span class="menu-label flex-1 ml-3 text-left">Logout</span>
                </button>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="get" class="hidden">
                @csrf
            </form>
        </div>

    </div>
</aside>
@endif