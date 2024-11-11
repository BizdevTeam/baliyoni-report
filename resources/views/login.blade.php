<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite('resources/css/app.css') <!-- Assuming you're using Laravel Mix/Vite for assets -->
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="flex w-4/5 max-w-4xl overflow-hidden bg-white shadow-lg rounded-lg">
    <!-- Left side with logo and decorative circles -->
    <div class="relative flex items-center justify-center w-1/2 bg-gray-200 p-8">
        <img src="{{ asset('path/to/baliyoni-logo.png') }}" alt="Baliyoni Group Logo" class="w-2/3 max-w-xs">
        
        <!-- Decorative circles -->
        <div class="absolute w-32 h-32 bg-gradient-to-br from-red-500 to-black rounded-full top-5 left-5"></div>
        <div class="absolute w-48 h-48 bg-gradient-to-br from-red-500 to-black rounded-full bottom-10 right-10"></div>
    </div>

    <!-- Right side with login form -->
    <div class="flex flex-col justify-center w-1/2 p-8">
        <h2 class="text-3xl font-semibold text-gray-800">Log in</h2>
        <p class="mt-2 text-gray-600">Please login to your account.</p>

        <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">User Name</label>
                <input type="text" id="username" name="username" placeholder="example@gmail.com" 
                       class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" placeholder="***************" 
                       class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <button type="submit" class="w-full px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:bg-red-600">
                Login
            </button>
        </form>
    </div>
</div>

</body>
</html>
