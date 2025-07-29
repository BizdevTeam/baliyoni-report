<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function index()
    {
        return view('login');
    }

    /**
     * Tangani request login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input: ganti 'email' → 'name'
        $credentials = $request->validate([
            'name'     => 'required|string',
            'password' => 'required',
        ], [
            'name.required'     => 'Username Wajib Diisi',
            'password.required' => 'Password Wajib Diisi',
        ]);

        // 2. Coba autentikasi dengan field 'name'
        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors('Username & Password tidak cocok')
                ->withInput();
        }

        // 3. Regenerate session (security best practice)
        $request->session()->regenerate();

        // 4. Cek role masih sama
        $role = Auth::user()->role;
        if (! in_array($role, $this->allowedRoles(), true)) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors('Role tidak valid');
        }

        // 5. Redirect ke intended atau /admin/app
        return redirect()->intended('/admin/app');
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Daftar role yang valid.
     */
    private function allowedRoles(): array
    {
        return [
            'superadmin',
            'marketing',
            'it',
            'procurement',
            'accounting',
            'support',
            'hrga',
            'spi',
        ];
    }
}
