<?php
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::middleware(['guest'])->group(function() {
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

Route::middleware(['auth'])->group(function() {

    Route::get('/admin', [AdminController::class, 'index'])->middleware('UserAccess:index');
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->middleware('UserAccess:spi');
    Route::get('/logout', [SessionController::class, 'logout']);
});

=======
Route::get('/', [SessionController::class, 'index']);
Route::get('/login', [SessionController::class, 'index'])->name('login');
Route::get('/sidebar', function () {
    return view('sidebar');
});
>>>>>>> 4139c46785f1db488a140c8eb2f1836e2bdcdd54
