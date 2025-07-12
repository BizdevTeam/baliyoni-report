<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Company Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100">
    <div class="wrapper">
        
        {{-- Asumsi Anda memiliki komponen sidebar dan navbar --}}
        <x-sidebar />
        <x-navbar />

        {{-- Konten Utama --}}
       <div id="admincontent" class="p-4 sm:p-6 lg:p-8">
            <h1 class="flex text-3xl font-bold text-gray-800 justify-center mt-4 mb-8">Manajemen User</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex flex-col sm:flex-row items-center justify-between mb-4">
                    <!-- Tombol Tambah User -->
                    <button
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center gap-2 mb-2 sm:mb-0"
                        data-modal-target="#addUserModal">
                        <i class="fa fa-plus"></i>
                        <span>Tambah User</span>
                    </button>

                    <!-- Search -->
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="flex items-center border border-gray-300 rounded-lg p-2 w-full">
                            <input type="text" name="search" placeholder="Cari berdasarkan nama atau email..." value="{{ request('search') }}"
                                class="flex-1 border-none focus:ring-0 focus:outline-none text-gray-700 placeholder-gray-400" />
                            <button type="submit"
                                class="text-gray-500 hover:text-blue-600"
                                aria-label="Search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notifikasi Sukses/Error -->
                @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif
                @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
                @endif
                @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Oops! Ada beberapa error:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif


                <!-- Tabel User -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300" id="data-table">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="border border-gray-300 px-4 py-3 text-left uppercase font-semibold text-sm">Nama</th>
                                <th class="border border-gray-300 px-4 py-3 text-left uppercase font-semibold text-sm">Email</th>
                                <th class="border border-gray-300 px-4 py-3 text-center uppercase font-semibold text-sm">Role</th>
                                <th class="border border-gray-300 px-4 py-3 text-center uppercase font-semibold text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">{{ strtoupper($user->role) }}</span>
                                </td>
                                <td class="border border-gray-300 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Tombol Edit -->
                                        <button class="px-2 py-1 text-yellow-500 hover:text-yellow-700" title="Edit" data-modal-target="#editUserModal{{ $user->id }}">
                                            <i class="fa fa-pen-to-square"></i>
                                        </button>
                                        <!-- Form Hapus -->
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-red-600 hover:text-red-800" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                <div class="m-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>


    <!-- Modal untuk Tambah User -->
    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="addUserModal">
        <div class="bg-white w-11/12 max-w-2xl p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Tambah User Baru</h3>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="add-name" class="block text-sm font-medium">Nama</label>
                        <input type="text" name="name" id="add-name" class="w-full p-2 border rounded mt-1" required>
                    </div>
                    <div>
                        <label for="add-email" class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="add-email" class="w-full p-2 border rounded mt-1" required>
                    </div>
                    <div>
                        <label for="add-role" class="block text-sm font-medium">Role</label>
                        <select name="role" id="add-role" class="w-full p-2 border rounded mt-1" required>
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="add-password" class="block text-sm font-medium">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="add-password" class="w-full p-2 border rounded mt-1 pr-10" required>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label for="add-password_confirmation" class="block text-sm font-medium">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="add-password_confirmation" class="w-full p-2 border rounded mt-1 pr-10" required>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-modal-close>Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Edit User (dibuat dalam loop) -->
    @foreach ($users as $user)
    <div class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="editUserModal{{ $user->id }}">
        <div class="bg-white w-11/12 max-w-2xl p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Edit User</h3>
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit-name-{{$user->id}}" class="block text-sm font-medium">Nama</label>
                        <input type="text" name="name" id="edit-name-{{$user->id}}" class="w-full p-2 border rounded mt-1" value="{{ $user->name }}" required>
                    </div>
                    <div>
                        <label for="edit-email-{{$user->id}}" class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="edit-email-{{$user->id}}" class="w-full p-2 border rounded mt-1" value="{{ $user->email }}" required>
                    </div>
                    <div>
                        <label for="edit-role-{{$user->id}}" class="block text-sm font-medium">Role</label>
                        <select name="role" id="edit-role-{{$user->id}}" class="w-full p-2 border rounded mt-1" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-sm text-gray-600 pt-2">Kosongkan password jika tidak ingin mengubahnya.</p>
                    <div>
                        <label for="edit-password-{{$user->id}}" class="block text-sm font-medium">Password Baru</label>
                         <div class="relative">
                            <input type="password" name="password" id="edit-password-{{$user->id}}" class="w-full p-2 border rounded mt-1 pr-10">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label for="edit-password_confirmation-{{$user->id}}" class="block text-sm font-medium">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="edit-password_confirmation-{{$user->id}}" class="w-full p-2 border rounded mt-1 pr-10">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" data-modal-close>Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Logika untuk membuka modal
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });
        });

        // Logika untuk menutup modal
        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.fixed');
                if (modal) {
                    modal.classList.add('hidden');
                }
            });
        });
        
        // Menutup modal dengan klik di luar area modal
        window.addEventListener('click', function(event) {
            if (event.target.matches('.fixed')) {
                event.target.classList.add('hidden');
            }
        });
    });

    // Fungsi untuk toggle visibilitas password
    function togglePasswordVisibility(spanElement) {
        const input = spanElement.previousElementSibling;
        const icon = spanElement.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
</html>