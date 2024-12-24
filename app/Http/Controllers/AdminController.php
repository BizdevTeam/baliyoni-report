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
        return view('layouts.admin');
    }
    function marketing()
    {
        return view('layouts.marketing');
    }
    function it()
    {
        return view('layouts.it');
    }
    function procurement(){
        return view('layouts.procurement');
    }
    function accounting(){
        return view('layouts.accounting');
    }
    function support(){
        return view('layouts.support');
    }
    function hrga(){
        return view('layouts.hrga');
    }
    function spi(){
        return view('layouts.spi');
    }
}
