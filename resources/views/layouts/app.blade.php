<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SUPERADMIN</title>

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
                body > *:not(#page-loader) {
                    opacity: 0;
                }
                body.loaded > *:not(#page-loader) {
                    opacity: 1;
                    transition: opacity 0.3s ease-in-out;
                }
                body.loaded #page-loader {
                    display: none !important;
                }
            </style>
        `);
    </script>
    <style>
        /* Styling agar numbered list & bullet list tetap tampil di tabel */
        .content-html ol {
        list-style-type: decimal;
        margin-left: 20px;
        }
    
        .content-html ul {
        list-style-type: disc;
        margin-left: 20px;
        }
    
        .content-html li {
        margin-bottom: 4px;
        }
    </style>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- CSS Plugins -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Tailwind & Vite CSS -->
    @vite(['resources/css/tailwind.css', 'resources/css/custom.css'])

    <!-- JS Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed overflow-x-hidden">

    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Navbar -->
        <x-navbar />

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
            function checkTailwindLoaded() {
                const test = document.createElement('div');
                test.className = 'hidden md:block';
                test.style.cssText = 'position: absolute; visibility: hidden;';
                document.body.appendChild(test);
                const isTailwind = window.getComputedStyle(test).display === 'none';
                document.body.removeChild(test);
                return isTailwind;
            }

            function removeLoader() {
                document.body.classList.add('loaded');
                const loader = document.getElementById('page-loader');
                if (loader && loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                }
            }

            if (checkTailwindLoaded()) {
                removeLoader();
            } else {
                let attempts = 0;
                const interval = setInterval(() => {
                    if (checkTailwindLoaded() || attempts >= 5) {
                        clearInterval(interval);
                        removeLoader();
                    }
                    attempts++;
                }, 200);

                // Fallback
                setTimeout(removeLoader, 20000);
            }
        });

        window.addEventListener('load', function () {
            document.body.classList.add('loaded');
        });
    </script>
</body>

</html>
