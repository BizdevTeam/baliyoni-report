<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // 
    function index(){
        return view('components.app');
    }
    function marketing(){
        echo "Hello Welcome Back Marketing!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function it(){
        echo "Hello Welcome Back IT!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function procurement(){
        echo "Hello Welcome Back Procurement!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function accounting(){
        echo "Hello Welcome Back Accounting!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function support(){
        echo "Hello Welcome Back Support!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function hrga(){
        echo "Hello Welcome Back HRGA!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
    function spi(){
        echo "Hello Welcome Back SPI!";
        echo "<h1>" . Auth::user()->name . "</h1>" ;
        echo "<a href='/logout' >Logout</a>";
    }
}
