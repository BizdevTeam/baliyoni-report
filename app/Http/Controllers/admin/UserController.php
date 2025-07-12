<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan halaman manajemen pengguna (daftar & form).
     */
    public function index(Request $request)
    {
        // Mulai query builder untuk User, kecuali user yang sedang login
        $query = User::where('id', '!=', auth()->id());

        // Tambahkan logika pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ambil data user dengan paginasi dan tambahkan query string ke link paginasi
        // agar pencarian tidak hilang saat pindah halaman
        $users = $query->latest()->paginate(10)->withQueryString();
        
        // Siapkan variabel untuk form
        $roles = ['superadmin', 'marketing', 'it', 'procurement', 'accounting', 'support', 'hrga', 'hrd', 'spi'];
        $userToEdit = null;

        // Cek jika ada aksi 'edit' di URL
        if ($request->has('action') && $request->action == 'edit' && $request->has('id')) {
            $userToEdit = User::find($request->id);
        }

        // Kirim semua data yang diperlukan ke satu view
        return view('admin.users', compact('users', 'roles', 'userToEdit'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['superadmin', 'marketing', 'it', 'procurement', 'accounting', 'support', 'hrga', 'hrd', 'spi'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Redirect kembali ke halaman utama
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['superadmin', 'marketing', 'it', 'procurement', 'accounting', 'support', 'hrga', 'hrd', 'spi'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}