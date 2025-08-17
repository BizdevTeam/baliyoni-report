<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ARTWORK</title>

    <!-- Page Loader (Ditempatkan di awal dengan JS) -->
    <script>
        document.write(`
            <div id="page-loader" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #ffffff;
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
            ">
                <div style="text-align: center;">
                    <div style="
                        width: 40px;
                        height: 40px;
                        margin: 0 auto;
                        border: 3px solid #f3f3f3;
                        border-radius: 50%;
                        border-top: 3px solid #3498db;
                        animation: spin 1s linear infinite;
                    "></div>
                    <p style="margin-top: 10px; font-family: sans-serif;">Loading...</p>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                body {
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                }
                body.loaded {
                    opacity: 1;
                }
            </style>
        `);
    </script>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- CSS Plugins -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Vite CSS (Handles Tailwind and custom CSS) -->
    @vite(['resources/css/tailwind.css', 'resources/css/custom.css'])

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed overflow-x-hidden">

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="z-20">
            <x-sidebar />
        </div>

        <!-- Navbar -->
        <div class="z-30">
            <x-navbar />
        </div>

        <!-- Main Content -->
        <main>
            <div class="py-3">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Modal Loader (Untuk penggunaan interaktif seperti AJAX) -->
    <div id="loadingModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-red-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p class="text-gray-700 text-sm">Mohon tunggu...</p>
        </div>
    </div>

    <!-- Local JS -->
    <script src="{{ asset('/templates/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/templates/dist/js/adminlte.js') }}"></script>

    @vite('resources/js/app.js')

    <!-- Loader Dismiss Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loadingModal = document.getElementById('loadingModal');
            if (loadingModal) {
                loadingModal.classList.add('hidden');
            }

            document.body.classList.add('loaded');
        });

        window.onload = function () {
            setTimeout(finalizePageLoad, 100); 
        };

        setTimeout(finalizePageLoad, 10000); 

        function finalizePageLoad() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300); 
            }
        }
    </script>
</body>

</html>
