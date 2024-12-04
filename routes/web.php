<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LaporanCutiController;
use App\Http\Controllers\LaporanIjasaController;
use App\Http\Controllers\LaporanIzinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\LaporanPtBosController;
use App\Http\Controllers\LaporanSakitController;
use App\Http\Controllers\LaporanTerlambatController;
use App\Http\Controllers\RekapPenjualanController;
use App\Models\LaporanIzin;

Route::middleware(['web'])->group(function () {
    // Marketing Routes
    // Route to display the laporan paket administrasi page (view)
    Route::get('marketings/laporanpaketadministrasi', [LaporanPaketAdministrasiController::class, 'index'])->name('marketings.laporanpaketadministrasi');
    // Route to store new laporan paket administrasi data
    Route::post('marketings/laporanpaketadministrasi/store', [LaporanPaketAdministrasiController::class, 'store'])->name('marketings.laporanpaketadministrasi.store');
    // Route to update existing laporan paket administrasi data
    Route::put('marketings/laporanpaketadministrasi/update/{id}', [LaporanPaketAdministrasiController::class, 'update'])->name('marketings.laporanpaketadministrasi.update');
    // Route to fetch laporan paket administrasi data (API for table)
    Route::get('marketings/laporanpaketadministrasi/data', [LaporanPaketAdministrasiController::class, 'data'])->name('marketings.laporanpaketadministrasi.data');
    // Route to fetch filtered laporan paket administrasi data (optional for filtering purposes)
    Route::get('marketings/laporanpaketadministrasi/filter', [LaporanPaketAdministrasiController::class, 'filterData'])->name('marketings.laporanpaketadministrasi.filter');
    // Route to delete laporan paket administrasi data
    Route::delete('marketings/laporanpaketadministrasi/destroy/{id}', [LaporanPaketAdministrasiController::class, 'destroy'])->name('marketings.laporanpaketadministrasi.destroy');
    
    // Marketing Routes end
        Route::get('marketings/rekappenjualan', [RekapPenjualanController::class, 'index'])->name('rekap-penjualan.index'); // Halaman utama
        Route::get('marketings/rekappenjualan/data', [RekapPenjualanController::class, 'getData'])->name('rekap-penjualan.getData'); // Ambil data dengan filter
        Route::get('marketings/rekappenjualan/detail', [RekapPenjualanController::class, 'data'])->name('rekap-penjualan.data'); // Ambil data spesifik
        Route::post('marketings/rekappenjualan', [RekapPenjualanController::class, 'store'])->name('rekap-penjualan.store'); // Simpan data baru
        Route::put('marketings/rekappenjualan/{id}', [RekapPenjualanController::class, 'update'])->name('rekap-penjualan.update'); // Perbarui data
        Route::delete('marketings/rekappenjualan/{id}', [RekapPenjualanController::class, 'destroy'])->name('rekap-penjualan.destroy'); // Hapus data
    
    Route::get('hrga/laporansakit', [LaporanSakitController::class, 'index'])
        ->name('hrga.laporansakit');
    
    Route::post('hrga/laporansakit/store', [LaporanSakitController::class, 'store'])
        ->name('hrga.laporansakit.store');
    
    Route::put('hrga/laporansakit/update/{id}', [LaporanSakitController::class, 'update'])
        ->name('hrga.laporansakit.update');
    
    Route::get('hrga/laporansakit/data', [LaporanSakitController::class, 'getData'])
        ->name('hrga.laporansakit.getData');
        
    Route::delete('hrga/laporansakit/destroy/{id}', [LaporanSakitController::class, 'destroy'])
        ->name('hrga.laporansakit.destroy');

    //Route untuk laporan izin hrga
    Route::get('hrga/laporanizin', [LaporanIzinController::class, 'index'])
        ->name('hrga.laporanizin');
    
    Route::post('hrga/laporanizin/store', [LaporanIzinController::class, 'store'])
        ->name('hrga.laporanizin.store');
    
    Route::put('hrga/laporanizin/update/{id}', [LaporanIzinController::class, 'update'])
        ->name('hrga.laporanizin.update');
    
    Route::get('hrga/laporanizin/data', [LaporanIzinController::class, 'getData'])
        ->name('hrga.laporanizin.getData');
     
    Route::delete('hrga/laporanizin/destroy/{id}', [LaporanIzinController::class, 'destroy'])
        ->name('hrga.laporanizin.destroy');

    //Route untuk laporan cuti hrga
    Route::get('hrga/laporancuti', [LaporanCutiController::class, 'index'])
        ->name('hrga.laporancuti');
    
    Route::post('hrga/laporancuti/store', [LaporanCutiController::class, 'store'])
        ->name('hrga.laporancuti.store');
    
    Route::put('hrga/laporancuti/update/{id}', [LaporanCutiController::class, 'update'])
        ->name('hrga.laporancuti.update');
    
    Route::get('hrga/laporancuti/data', [LaporanCutiController::class, 'getData'])
        ->name('hrga.laporancuti.getData');
     
    Route::delete('hrga/laporancuti/destroy/{id}', [LaporanCutiController::class, 'destroy'])
        ->name('hrga.laporancuti.destroy');

     //Route untuk laporan terlambat hrga
    Route::get('hrga/laporanterlambat', [LaporanTerlambatController::class, 'index'])
        ->name('hrga.laporaterlambat');
    
    Route::post('hrga/laporanterlambat/store', [LaporanTerlambatController::class, 'store'])
        ->name('hrga.laporanterlambat.store');
    
    Route::put('hrga/laporanterlambat/update/{id}', [LaporanTerlambatController::class, 'update'])
        ->name('hrga.laporanterlambat.update');
    
    Route::get('hrga/laporanterlambat/data', [LaporanTerlambatController::class, 'getData'])
        ->name('hrga.laporanterlambat.getData');
     
    Route::delete('hrga/laporanterlambat/destroy/{id}', [LaporanTerlambatController::class, 'destroy'])
        ->name('hrga.laporanterlambat.destroy');

    //Route untuk laporan ptboss hrga
    Route::get('hrga/laporanptbos', [LaporanPtBosController::class, 'index'])
        ->name('hrga.laporanptbos');
    
    Route::post('hrga/laporanptbos/store', [LaporanPtBosController::class, 'store'])
        ->name('hrga.laporanptbos.store');
    
    Route::put('hrga/laporanptbos/update/{id}', [LaporanPtBosController::class, 'update'])
        ->name('hrga.laporanptbos.update');
    
    Route::get('hrga/laporanptbos/data', [LaporanPtBosController::class, 'getData'])
        ->name('hrga.laporanptbos.getData');
     
    Route::delete('hrga/laporanptbos/destroy/{id}', [LaporanPtBosController::class, 'destroy'])
        ->name('hrga.laporanptbos.destroy');


        
    
    //Route untuk laporan ijasa hrga
    Route::get('hrga/laporanijasa', [LaporanIjasaController::class, 'index'])
        ->name('hrga.laporanijasa');
    
    Route::post('hrga/laporanijasa/store', [LaporanIjasaController::class, 'store'])
        ->name('hrga.laporanijasa.store');
    
    Route::put('hrga/laporanijasa/update/{id}', [LaporanIjasaController::class, 'update'])
        ->name('hrga.laporanijasa.update');
    
    Route::get('hrga/laporanijasa/data', [LaporanIjasaController::class, 'getData'])
        ->name('hrga.laporanijasa.getData');
     
    Route::delete('hrga/laporanijasa/destroy/{id}', [LaporanIjasaController::class, 'destroy'])
        ->name('hrga.laporanijasa.destroy');



// HRD Routes end
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
