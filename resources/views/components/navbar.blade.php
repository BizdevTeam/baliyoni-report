<nav id="navbar" class="main-header fixed top-0 left-0 right-0 bg-[#2c2e3e] text-white border-b border-gray-800 shadow-md transition-all duration-300 h-14 z-30">
    <div class="container mx-auto px-4 flex items-center justify-between h-full">
        <!-- Left: Sidebar Toggle and Home Link -->
        <div class="flex items-center space-x-2">
            <button id="toggle-sidebar" class="p-2 text-gray-300 hover:text-white focus:outline-none transition">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <a href="#" class="text-lg font-semibold text-white hover:text-red-600 transition">
                Home
            </a>
        </div>
        
        <!-- Right: User Info and Logout Dropdown -->
        <div class="flex items-center space-x-4">
            @if(Auth::check())
                <!-- TailwindCSS for dropdown visibility -->
                <div class="relative group">
                    <!-- User Info and Dropdown Trigger -->
                    <div class="flex items-center space-x-2 cursor-pointer">
                        <i class="fas fa-user text-gray-300 text-lg"></i>
                        <span class="text-white font-medium hidden sm:inline">{{ Auth::user()->name }}</span>
                        <i class="fas fa-caret-down text-gray-300 transition-transform duration-200 group-hover:rotate-180"></i>
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-40 opacity-0 group-hover:opacity-100 transition-all duration-200 ease-out transform group-hover:translate-y-0 -translate-y-2">
                        <div class="px-4 py-2 text-sm text-gray-700">
                            Signed in as <br>
                            <strong class="font-semibold">{{ Auth::user()->name }}</strong>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Logout Form -->
                        <form method="get" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                <i class="fas fa-sign-out-alt text-gray-500"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>

            @else
                <a href="{{ route('login') }}" class="flex items-center space-x-2">
                    <i class="fas fa-sign-in-alt text-gray-300 text-lg"></i>
                    <span class="text-white font-medium">Login</span>
                </a>
            @endif
        </div>
    </div>
</nav>
