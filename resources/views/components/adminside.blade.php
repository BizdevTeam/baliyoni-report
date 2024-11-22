<aside id="sidebar" class="w-64 transition-all duration-300 bg-gray-50 border-r border-gray-200 shadow-lg overflow-y-auto h-screen fixed top-0 left-0 z-20 flex flex-col" data-minimized="false">
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
                <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-md text-gray-700 hover:bg-red-600 hover:text-white transition">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="menu-label">Homepage</span>
                </a>
            </li>
            <li class="relative">
                <button type="button" class="flex items-center w-full px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition" aria-controls="dropdown-ecommerce" aria-expanded="false">
                    <i class="fas fa-shopping-cart text-gray-500"></i>
                    <span class="menu-label flex-1 ml-3 text-left">E-commerce</span>
                </button>
                <ul id="dropdown-ecommerce" class="hidden py-2 pl-6 space-y-2">
                    <li><a href="/marketings/laporanpaketadministrasi" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Products</a></li>
                    <li><a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Billing</a></li>
                    <li><a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Invoice</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>


