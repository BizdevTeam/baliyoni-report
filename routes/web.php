<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\RekapPenjualanController;

Route::middleware(['web'])->group(function () {
    // Marketing Routes
    // Route to display the laporan paket administrasi page (view)
    Route::get('marketings/laporanpaketadministrasi', [LaporanPaketAdministrasiController::class, 'index'])
        ->name('marketings.laporanpaketadministrasi');

    // Route to store new laporan paket administrasi data
    Route::post('marketings/laporanpaketadministrasi/store', [LaporanPaketAdministrasiController::class, 'store'])
        ->name('marketings.laporanpaketadministrasi.store');

    // Route to update existing laporan paket administrasi data
    Route::put('marketings/laporanpaketadministrasi/update/{id}', [LaporanPaketAdministrasiController::class, 'update'])
        ->name('marketings.laporanpaketadministrasi.update');

    // Route to fetch laporan paket administrasi data (API for table)
    Route::get('marketings/laporanpaketadministrasi/data', [LaporanPaketAdministrasiController::class, 'data'])
        ->name('marketings.laporanpaketadministrasi.data');

    // Route to fetch filtered laporan paket administrasi data (optional for filtering purposes)
    Route::get('marketings/laporanpaketadministrasi/filter', [LaporanPaketAdministrasiController::class, 'filterData'])
        ->name('marketings.laporanpaketadministrasi.filter');

    // Route to delete laporan paket administrasi data
    Route::delete('marketings/laporanpaketadministrasi/destroy/{id}', [LaporanPaketAdministrasiController::class, 'destroy'])
        ->name('marketings.laporanpaketadministrasi.destroy');
    // Marketing Routes end

    Route::get('marketings/rekappenjualan', [RekapPenjualanController::class, 'index'])
        ->name('marketings.rekappenjualan');

    // Route to store new laporan paket administrasi data
    Route::post('marketings/rekappenjualan/store', [RekapPenjualanController::class, 'store'])
        ->name('marketings.rekappenjualan.store');

    // Route to update existing laporan paket administrasi data
    Route::put('marketings/rekappenjualan/update/{id}', [RekapPenjualanController::class, 'update'])
        ->name('marketings.rekappenjualan.update');

    // Route to fetch laporan paket administrasi data (API for table)
    Route::get('marketings/rekappenjualan/data', [RekapPenjualanController::class, 'data'])
        ->name('marketings.rekappenjualan.data');

    // Route to fetch filtered laporan paket administrasi data (optional for filtering purposes)
    Route::get('marketings/rekappenjualan/filter', [RekapPenjualanController::class, 'filterData'])
        ->name('marketings.rekappenjualan.filterByYear');

    // Route to delete laporan paket administrasi data
    Route::delete('marketings/rekappenjualan/destroy/{id}', [RekapPenjualanController::class, 'destroy'])
        ->name('marketings.rekappenjualan.destroy');

    // Route::get('marketings/rekappenjualan', [RekapPenjualanController::class, 'index'])
    //     ->name('marketings.rekappenjualan');
    //     Route::get('/marketings/rekappenjualan/data', [RekapPenjualanController::class, 'getData']);    // Store new data
    // Route::post('/marketings/rekappenjualan/store', [RekapPenjualanController::class, 'store']);
    // // Update existing data
    // Route::put('/marketings/rekappenjualan/update/{id}', [RekapPenjualanController::class, 'update']);
    // // Delete data
    // Route::delete('/marketings/rekappenjualan/destroy/{id}', [RekapPenjualanController::class, 'destroy']);
});

Route::middleware(['guest'])->group(function () {
    // Guest routes for login
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    // Authenticated routes
    Route::get('/admin', [AdminController::class, 'index'])->name('layouts.admin')->middleware('UserAccess:superadmin');
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->middleware('UserAccess:spi');
    Route::get('/logout', [SessionController::class, 'logout']);
});
