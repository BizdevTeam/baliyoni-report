<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/App.jsx'])
    <title>Admin Dashboard</title>
    <!-- Include your styles and scripts -->
</head>
<body>
    <!-- Include Sidebar -->
    @include('components.sidebar')

    {{-- <!-- Include Navbar -->
    @include('partials.admin.navbar')

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Include Footer -->
    @include('partials.admin.footer') --}}
</body>
</html>
