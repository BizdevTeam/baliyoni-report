@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar User</h2>
        {{-- Tombol untuk mengarahkan ke halaman pembuatan user baru --}}
        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
            + Tambah User
        </a>
    </div>

    <!-- Bagian untuk menampilkan notifikasi sukses atau error -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabel untuk menampilkan data user -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/12 text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                    <th class="w-3/12 text-left py-3 px-4 uppercase font-semibold text-sm">Nama</th>
                    <th class="w-4/12 text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                    <th class="w-2/12 text-left py-3 px-4 uppercase font-semibold text-sm">Role</th>
                    <th class="w-2/12 text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                {{-- Loop melalui data pengguna yang dikirim dari controller --}}
                @forelse ($users as $user)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-4">{{ $user->id }}</td>
                        <td class="py-3 px-4">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4"><span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">{{ strtoupper($user->role) }}</span></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex item-center justify-center space-x-4">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold">Edit</a>
                                
                                {{-- Form untuk tombol Hapus --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Pesan jika tidak ada data user --}}
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Navigasi Paginasi -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
