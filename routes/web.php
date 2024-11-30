<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\RekapPenjualanController;
use App\Http\Controllers\StatusPaketController;


Route::middleware(['web'])->group(function () {
    // Rute Laporan Paket Administrasi
    Route::get('marketings/laporanpaketadministrasi', [LaporanPaketAdministrasiController::class, 'index'])
        ->name('marketings.laporanpaketadministrasi');
    Route::post('marketings/laporanpaketadministrasi/store', [LaporanPaketAdministrasiController::class, 'store'])
        ->name('marketings.laporanpaketadministrasi.store');
    Route::put('marketings/laporanpaketadministrasi/update/{id}', [LaporanPaketAdministrasiController::class, 'update'])
        ->name('marketings.laporanpaketadministrasi.update');
    Route::get('marketings/laporanpaketadministrasi/data', [LaporanPaketAdministrasiController::class, 'data'])
        ->name('marketings.laporanpaketadministrasi.data');
    Route::get('marketings/laporanpaketadministrasi/filter', [LaporanPaketAdministrasiController::class, 'filterData'])
        ->name('marketings.laporanpaketadministrasi.filter');
    Route::delete('marketings/laporanpaketadministrasi/destroy/{id}', [LaporanPaketAdministrasiController::class, 'destroy'])
        ->name('marketings.laporanpaketadministrasi.destroy');

    //rute rekap penjualan
    Route::get('marketings/rekappenjualan', [RekapPenjualanController::class, 'index'])
        ->name('marketings.rekappenjualan');
    Route::post('marketings/rekappenjualan/store', [RekapPenjualanController::class, 'store'])
        ->name('marketings.rekappenjualan.store');
    Route::put('marketings/rekappenjualan/update/{id}', [RekapPenjualanController::class, 'update'])
        ->name('marketings.rekappenjualan.update');
    Route::get('marketings/rekappenjualan/data', [RekapPenjualanController::class, 'data'])
        ->name('marketings.rekappenjualan.data');
    Route::get('marketings/rekappenjualan/filter', [RekapPenjualanController::class, 'filterData'])
        ->name('marketings.rekappenjualan.filterByYear');
    Route::delete('marketings/rekappenjualan/destroy/{id}', [RekapPenjualanController::class, 'destroy'])
        ->name('marketings.rekappenjualan.destroy');


    //rute Status Paket
    Route::get('marketings/statuspaket', [StatusPaketController::class, 'index'])
        ->name('marketings.statuspaket');
    Route::post('marketings/statuspaket/store', [StatusPaketController::class, 'store'])
        ->name('marketings.statuspaket.store');
    Route::put('marketings/statuspaket/update/{id}', [StatusPaketController::class, 'update'])
        ->name('marketings.statuspaket.update');
    Route::get('marketings/statuspaket/data', [StatusPaketController::class, 'data'])
        ->name('marketings.statuspaket.data');
    Route::get('marketings/statuspaket/filter', [StatusPaketController::class, 'filterData'])
        ->name('marketings.statuspaket.filter');
    Route::delete('marketings/statuspaket/destroy/{id}', [StatusPaketController::class, 'destroy'])
        ->name('marketings.statuspaket.destroy');
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
