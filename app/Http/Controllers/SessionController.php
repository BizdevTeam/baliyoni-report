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
                    return redirect('marketing');
                case 'it':
                    return redirect('it');
                case 'procurement':
                    return redirect('procurement');
                case 'accounting':
                    return redirect('accounting');
                case 'support':
                    return redirect('support');
                case 'hrga':
                    return redirect('hrga');
                case 'spi':
                    return redirect('spi');
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
