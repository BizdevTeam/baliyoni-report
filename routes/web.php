<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'index']);
Route::get('/login', [SessionController::class, 'index'])->name('login');
Route::get('/sidebar', function () {
    return view('sidebar');
});
