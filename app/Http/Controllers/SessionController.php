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
                    return redirect('admin');
                case 'marketing':
                    return redirect('admin/marketing');
                case 'it':
                    return redirect('admin/it');
                case 'procurement':
                    return redirect('admin/procurement');
                case 'accounting':
                    return redirect('admin/accounting');
                case 'support':
                    return redirect('admin/support');
                case 'hrga':
                    return redirect('admin/hrga');
                case 'hrd':
                    return redirect('admin/hrd');
                case 'spi':
                    return redirect('admin/spi');
                default:
                    Auth::logout();
                    return redirect('/')->withErrors('Invalid role');
            }
        } else {
            return redirect('/')->withErrors('Email & Password doesn\'t match our records')->withInput();
        }
    }

    function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
