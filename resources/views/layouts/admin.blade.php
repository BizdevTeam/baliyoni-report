<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SUPERADMIN</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- CSS Plugins -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Tailwind & Vite CSS -->
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>

    <!-- Local JS -->
    <script src="{{ asset('/templates/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/templates/dist/js/adminlte.js') }}"></script>

    @vite('resources/js/app.js')
</head>

<body class="hold-transition sidebar-mini layout-fixed overflow-x-hidden">
    <div class="wrapper">

        <!-- Sidebar -->
        <x-sidebar />

        <!-- Navbar -->
        <x-navbar />

        <!-- Main Content -->
        <x-admincontent>
            @yield('content')
        </x-admincontent>

    </div>
    <style>
        .ck-content ul, .ck-content ol {
            padding-left: 20px;
            margin: 5px 0;
        }
        .ck-content li {
            margin-bottom: 3px;
        }
        .ck-content p {
            margin: 5px 0;
        }
        .ck-content strong, .ck-content b {
            font-weight: bold;
        }
        .ck-content em, .ck-content i {
            font-style: italic;
        }
        /* Make sure table cells can expand to fit content */
        #adminspi td {
            vertical-align: top;
        }
    </style>
</body>

</html>
