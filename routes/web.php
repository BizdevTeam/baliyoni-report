<?php
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\RekapPenjualanController;

//marketing : 
Route::middleware(['web'])->group(function () {
    Route::get('marketings/laporanpaketadministrasi', [LaporanPaketAdministrasiController::class, 'index'])->name('marketings.laporanpaketadministrasi');
    Route::post('marketings/laporanpaketadministrasi/store', [LaporanPaketAdministrasiController::class, 'store'])->name('marketings.store');
    Route::get('marketings/laporanpaketadministrasi/data', [LaporanPaketAdministrasiController::class, 'data'])->name('marketings.data');
   
    
    Route::get('marketings/rekappenjualan', [LaporanPaketAdministrasiController::class, 'index'])
        ->name('marketings.rekappenjualan');

    // Route to store new laporan paket administrasi data
    Route::post('marketings/rekappenjualan/store', [LaporanPaketAdministrasiController::class, 'store'])
        ->name('marketings.rekappenjualan.store');

    // Route to update existing laporan paket administrasi data
    Route::put('/marketings/rekappenjualan/update/{id}', [RekapPenjualanController::class, 'update']);

    // Route to fetch laporan paket administrasi data (API for table)
    Route::get('marketings/rekappenjualan/data', [LaporanPaketAdministrasiController::class, 'data'])
        ->name('marketings.rekappenjualan.data');

    // Route to fetch filtered laporan paket administrasi data (optional for filtering purposes)
    Route::get('marketings/rekappenjualan/filter', [LaporanPaketAdministrasiController::class, 'filterData'])
        ->name('marketings.rekappenjualan.filter');

    // Route to delete laporan paket administrasi data
    Route::delete('/marketings/rekappenjualan/destroy/{id}', [RekapPenjualanController::class, 'destroy']);
    // Marketing Routes end
    });




Route::middleware(['guest'])->group(function() {
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

Route::middleware(['auth'])->group(function() {

    Route::get('/admin', [AdminController::class, 'index'])->name('layouts.admin')->middleware('UserAccess:superadmin');
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->middleware('UserAccess:spi');
    Route::get('/logout', [SessionController::class, 'logout']);
});






