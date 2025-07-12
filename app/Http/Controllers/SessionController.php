<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    //
    function index()
    {
        return view('login');
    }
    function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ],[
            'email.required'=>'Email Wajib Diisi',
            'password.required'=>'Password Wajib Diisi',
        ]);

        $infologin = [
            'email'=> $request->email,
            'password'=> $request->password,
        ];

        if(Auth::attempt($infologin)){
            $role = Auth::user()->role;
            switch ($role) {
                case 'superadmin':
                    return redirect('admin/app');
                case 'marketing':
                    return redirect('admin/app');
                case 'it':
                    return redirect('admin/app');
                case 'procurement':
                    return redirect('admin/app');
                case 'accounting':
                    return redirect('admin/app');
                case 'support':
                    return redirect('admin/app');
                case 'hrga':
                    return redirect('admin/app');
                case 'spi':
                    return redirect('admin/app');
                default:
                    Auth::logout();
                    return redirect('/')->withErrors('Invalid role');
            }
        } else {
            return redirect('/')->withErrors('Email & Password doesn\'t match our records')->withInput();
        }
    }

    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
