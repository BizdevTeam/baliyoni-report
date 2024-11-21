{{-- <aside id="sidebar" class="w-64 sm:w-16 lg:w-64 transition-all duration-300 bg-gray-50 border-r border-gray-200 shadow-lg overflow-y-auto h-screen flex flex-col" data-minimized="false">
    <!-- Logo Section -->
    <div class="mt-8 mb-8 pb-3 flex justify-center">
        <div id="logo-full" class="logo w-40 h-auto sm:hidden lg:block">
            <img src="images/baliyoni.png" class="w-full" alt="Logo Full">
        </div>
        <div id="logo-mini" class="logo w-12 h-auto hidden sm:block lg:hidden">
            <img src="images/baliyoni-mini.png" class="w-full" alt="Logo Mini">
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-4">
        <ul class="flex flex-col space-y-2">
            <!-- Single Menu Item -->
            <li>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-md text-gray-700 hover:bg-red-600 hover:text-white transition">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="menu-label">Homepage</span>
                </a>
            </li>

            <!-- Dropdown Menu -->
            <li class="relative">
                <button type="button" class="flex items-center w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition focus:outline-none" aria-controls="dropdown-ecommerce" aria-expanded="false">
                    <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="menu-label flex-1 ml-3 text-left">E-commerce</span>
                    <svg class="w-6 h-6 transition-transform" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <ul id="dropdown-ecommerce" class="hidden py-2 pl-8 space-y-2">
                    <li>
                        <a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Products</a>
                    </li>
                    <li>
                        <a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Billing</a>
                    </li>
                    <li>
                        <a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Invoice</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside> --}}

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
                    <li><a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Products</a></li>
                    <li><a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Billing</a></li>
                    <li><a href="#" class="block px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition">Invoice</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>


