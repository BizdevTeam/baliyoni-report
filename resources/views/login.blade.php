<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite('resources/css/app.css') <!-- Assuming you're using Laravel Mix/Vite for assets -->
</head>
<body class="flex items-center justify-center min-h-screen backdrop-blur-xl">

<div class="z-50 flex w-1/2 max-w-full max-h-full overflow-hidden bg-white shadow-lg rounded-lg">
    <!-- Left side with logo and decorative circles -->
    <div class="relative flex items-center justify-center w-1/2 bg-gray-200 p-8">
        <img src="{{ asset('asset/baliyoni.png') }}" alt="Baliyoni Group Logo" class="w-2/3 max-w-xs">
        
        <!-- Decorative circles -->
        <div class="z-1 absolute w-32 h-32 bg-gradient-to-b from-black from-15% via-red-600 via-75% rounded-full -top-2 -right-2"></div>
        <div class="z-1 absolute w-16 h-16 bg-gradient-to-b from-black to-red-600 to-75% rounded-full -top-4 -right-4 drop-shadow"></div>
        <div class="z-1 absolute w-16 h-16 bg-gradient-to-t from-black to-red-600 to-75% rounded-full bottom-16 left-16 drop-shadow"></div>
        <div class="z-1 absolute w-32 h-32 bg-gradient-to-b from-black to-red-600 rounded-full -bottom-4 -left-4"></div>
        
    </div>

    <!-- Right side with login form -->
    <div class="z-50 flex flex-col justify-center w-1/2 p-8  bg-white shadow-lg">
        <h2 class="text-3xl font-bold text-gray-800">Log in</h2>
        <p class=" text-gray-600">Please login to your account.</p>

        @if($errors->any())
        <div class="mt-2 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:text-red-600" role="alert">
            <ul>
                @foreach($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <form action="{{ route('login') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">User Name</label>
                <input type="text" id="email" name="email" placeholder="example@gmail.com" value="{{ old('email')}}" 
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
