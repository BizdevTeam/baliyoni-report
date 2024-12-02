<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\RekapPenjualanController;
use App\Http\Controllers\StatusPaketController;
use App\Http\Controllers\LaporanPerInstansiController;
use App\Http\Controllers\RekapPenjualanPerusahaanController;
use App\Http\Controllers\LaporanPembelianHoldingController;


Route::middleware(['web'])->group(function () {
    //MARKETING
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

    //RUTE LAPORAN PERINSTANSI
    Route::get('marketings/laporanperinstansi', [LaporanPerInstansiController::class, 'index'])
        ->name('marketings.laporanperinstansi');
    Route::post('marketings/laporanperinstansi/store', [LaporanPerInstansiController::class, 'store'])
        ->name('marketings.laporanperinstansi.store');
    Route::put('marketings/laporanperinstansi/update/{id}', [LaporanPerInstansiController::class, 'update'])
        ->name('marketings.laporanperinstansi.update');
    Route::get('marketings/laporanperinstansi/data', [LaporanPerInstansiController::class, 'data'])
        ->name('marketings.laporanperinstansi.data');
    Route::get('marketings/laporanperinstansi/filter', [LaporanPerInstansiController::class, 'filterData'])
        ->name('marketings.laporanperinstansi.filter');
    Route::delete('marketings/laporanperinstansi/destroy/{id}', [LaporanPerInstansiController::class, 'destroy'])
        ->name('marketings.laporanperinstansi.destroy');

     //RUTE LAPORAN PENJUALAN PERUSAHAAN
    Route::get('marketings/rekappenjualanperusahaan', [RekapPenjualanPerusahaanController::class, 'index'])
        ->name('marketings.rekappenjualanperusahaan');
    Route::post('marketings/rekappenjualanperusahaan/store', [RekapPenjualanPerusahaanController::class, 'store'])
        ->name('marketings.rekappenjualanperusahaan.store');
    Route::put('marketings/rekappenjualanperusahaan/update/{id}', [RekapPenjualanPerusahaanController::class, 'update'])
        ->name('marketings.rekappenjualanperusahaan.update');
    Route::get('marketings/rekappenjualanperusahaan/data', [RekapPenjualanPerusahaanController::class, 'data'])
        ->name('marketings.rekappenjualanperusahaan.data');
    Route::get('marketings/rekappenjualanperusahaan/filter', [RekapPenjualanPerusahaanController::class, 'filterData'])
        ->name('marketings.rekappenjualanperusahaan.filter');
    Route::delete('marketings/rekappenjualanperusahaan/destroy/{id}', [RekapPenjualanPerusahaanController::class, 'destroy'])
        ->name('marketings.rekappenjualanperusahaan.destroy');  
    //MARKETING  
    
    Route::get('procurements/laporanpembelianholding', [LaporanPembelianHoldingController::class, 'index'])
    ->name('procurements/laporanpembelianholding');
Route::post('procurements/laporanpembelianholding/store', [LaporanPembelianHoldingController::class, 'store'])
    ->name('procurements/laporanpembelianholding.store');
Route::put('procurements/laporanpembelianholding/update/{id}', [LaporanPembelianHoldingController::class, 'update'])
    ->name('procurements/laporanpembelianholding.update');
Route::get('procurements/laporanpembelianholding/data', [LaporanPembelianHoldingController::class, 'data'])
    ->name('procurements/laporanpembelianholding.data');
Route::get('procurements/laporanpembelianholding/filter', [LaporanPembelianHoldingController::class, 'filterData'])
    ->name('procurements/laporanpembelianholding.filter');
Route::delete('procurements/laporanpembelianholding/destroy/{id}', [LaporanPembelianHoldingController::class, 'destroy'])
    ->name('procurements/laporanpembelianholding.destroy');  
//MARKETING 
    

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
