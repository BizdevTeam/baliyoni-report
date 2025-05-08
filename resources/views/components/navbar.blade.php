<nav id="navbar"
     class="main-header fixed top-0 left-0 right-0
            bg-[#2c2e3e] text-white border-b border-gray-800 shadow-md
            transition-all duration-300 h-14 z-20">
    <div class="container px-4 flex items-center justify-between h-full">
        <!-- Left: Sidebar Toggle and Home Link -->
        <div class="flex items-center space-x-2">
            <button id="toggle-sidebar" class="p-2 text-gray-300 hover:text-white focus:outline-none transition">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <a href="#" class="text-lg font-semibold text-white hover:text-red-600 transition">
                Home
            </a>
        </div>
        
            <!-- Search Bar -->
            <div id="navbar-right" class="flex items-center space-x-4 mr-4 absolute right-2">
                <!-- User Info -->
                <i class="fas fa-user text-gray-300 text-lg"></i>
                
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
            </div>
            
        </div>
    </div>
</nav>
