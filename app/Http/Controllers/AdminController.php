<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // 
    function index()
    {
        return view('layouts.app');
    }
    function marketing()
    {
        return view('layouts.app');
    }
    function it()
    {
        return view('layouts.app');
    }
    function procurement()
    {
        return view('layouts.app');
    }
    function accounting(){
        return view('layouts.app');
    }
    function support(){
        return view('layouts.app');
    }
    function hrga(){
        return view('layouts.app');
    }
    function spi(){
        return view('layouts.app');
    }
}
