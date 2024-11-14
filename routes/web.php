<?php
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'index']);
Route::get('/login', [SessionController::class, 'index'])->name('login');