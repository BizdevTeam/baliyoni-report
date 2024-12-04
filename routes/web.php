<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\LaporanPpnController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\StatusPaketController;
use App\Http\Controllers\ItBizdevDataController;
use App\Http\Controllers\LaporanRasioController;
use App\Http\Controllers\LaporanNeracaController;
use App\Http\Controllers\RekapPenjualanController;
use App\Http\Controllers\ItBizdevBulananController;
use App\Http\Controllers\LaporanLabaRugiController;
use App\Http\Controllers\LaporanNegosiasiController;
use App\Http\Controllers\LaporanTaxPlaningController;
use App\Http\Controllers\ItMultimediaTiktokController;
use App\Http\Controllers\LaporanPerInstansiController;
use App\Http\Controllers\ItMultimediaInstagramController;
use App\Http\Controllers\LaporanPembelianOutletController;
use App\Http\Controllers\LaporanPembelianHoldingController;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\RekapPenjualanPerusahaanController;




Route::middleware(['web'])->group(function () {
    // Accounting
    Route::prefix('admin/accounting')->group(function() {
        Route::resource('labarugi', LaporanLabaRugiController::class);
        Route::resource('neraca', LaporanNeracaController::class);
        Route::resource('rasio', LaporanRasioController::class);
        Route::resource('taxplaning', LaporanTaxPlaningController::class);
        Route::resource('laporanppn', LaporanPpnController::class);
    });

    // IT
    Route::prefix('admin/it')->group(function () {
        Route::resource('instagram', ItMultimediaInstagramController::class);
        Route::resource('tiktok', ItMultimediaTiktokController::class);
        Route::resource('bizdevbulanan', ItBizdevBulananController::class);
        Route::prefix('bizdevbulanan/{bizdevbulanan_id}')->group(function () {
            Route::get('bizdevdata', [ItBizdevDataController::class, 'index'])->name('bizdevdata.index');
            Route::get('bizdevdata/create', [ItBizdevDataController::class, 'create'])->name('bizdevdata.create');
            Route::post('bizdevdata/store', [ItBizdevDataController::class, 'store'])->name('bizdevdata.store');
            Route::get('bizdevdata/{id_bizdevdata}/edit', [ItBizdevDataController::class, 'edit'])->name('bizdevdata.edit');
            Route::put('bizdevdata/{id_bizdevdata}/update', [ItBizdevDataController::class, 'update'])->name('bizdevdata.update');
            Route::delete('bizdevdata/{id_bizdevdata}/destroy', [ItBizdevDataController::class, 'destroy'])->name('bizdevdata.destroy');
        });
    });

    // Menampilkan halaman laporan paket administrasi
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
    

//PROCUREMENT

//LAPORAN PEMBELIAN HOLDING
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

//LAPORAN STOK
Route::get('procurements/laporanstok', [LaporanStokController::class, 'index'])
->name('procurements.laporanstok');
Route::post('procurements/laporanstok/store', [LaporanStokController::class, 'store'])
->name('procurements.laporanstok.store');
Route::put('procurements/laporanstok/update/{id}', [LaporanStokController::class, 'update'])
->name('procurements.laporanstok.update');
Route::get('procurements/laporanstok/data', [LaporanStokController::class, 'data'])
->name('procurements.laporanstok.data');
Route::get('procurements/laporanstok/filter', [LaporanStokController::class, 'filterData'])
->name('procurements.laporanstok.filterByYear');
Route::delete('procurements/laporanstok/destroy/{id}', [LaporanStokController::class, 'destroy'])
->name('procurements.laporanstok.destroy');

//LAPORAN PEMBELIAN OUTLET
Route::get('procurements/laporanpembelianoutlet', [LaporanPembelianOutletController::class, 'index'])
->name('procurements.laporanpembelianoutlet');
Route::post('procurements/laporanpembelianoutlet/store', [LaporanPembelianOutletController::class, 'store'])
->name('procurements.laporanpembelianoutlet.store');
Route::put('procurements/laporanpembelianoutlet/update/{id}', [LaporanPembelianOutletController::class, 'update'])
->name('procurements.laporanpembelianoutlet.update');
Route::get('procurements/laporanpembelianoutlet/data', [LaporanPembelianOutletController::class, 'data'])
->name('procurements.laporanpembelianoutlet.data');
Route::get('procurements/laporanpembelianoutlet/filter', [LaporanPembelianOutletController::class, 'filterData'])
->name('procurements.laporanpembelianoutlet.filterByYear');
Route::delete('procurements/laporanpembelianoutlet/destroy/{id}', [LaporanPembelianOutletController::class, 'destroy'])
->name('procurements.laporanpembelianoutlet.destroy');

//LAPORAN NEGOSIASI
Route::get('procurements/laporannegosiasi', [LaporanNegosiasiController::class, 'index'])
->name('procurements.laporannegosiasi');
Route::post('procurements/laporannegosiasi/store', [LaporanNegosiasiController::class, 'store'])
->name('procurements.laporannegosiasi.store');
Route::put('procurements/laporannegosiasi/update/{id}', [LaporanNegosiasiController::class, 'update'])
->name('procurements.laporannegosiasi.update');
Route::get('procurements/laporannegosiasi/data', [LaporanNegosiasiController::class, 'data'])
->name('procurements.laporannegosiasi.data');
Route::get('procurements/laporannegosiasi/filter', [LaporanNegosiasiController::class, 'filterData'])
->name('procurements.laporannegosiasi.filterByYear');
Route::delete('procurements/laporannegosiasi/destroy/{id}', [LaporanNegosiasiController::class, 'destroy'])
->name('procurements.laporannegosiasi.destroy');
 
//ACCOUNTINGS
//LAPORAN KAS HUTANG PIUTANG STOK
Route::get('accountings/kashutangpiutangstok', [KasHutangPiutangStokController::class, 'index'])
->name('accountings.kashutangpiutangstok');
Route::post('accountings/kashutangpiutangstok/store', [KasHutangPiutangStokController::class, 'store'])
->name('accountings.kashutangpiutangstok.store');
Route::put('accountings/kashutangpiutangstok/update/{id}', [KasHutangPiutangStokController::class, 'update'])
->name('accountings.kashutangpiutangstok.update');
Route::get('accountings/kashutangpiutangstok/data', [KasHutangPiutangStokController::class, 'data'])
->name('accountings.kashutangpiutangstok.data');
Route::get('accountings/kashutangpiutangstok/filter', [KasHutangPiutangStokController::class, 'filterData'])
->name('accountings.kashutangpiutangstok.filterByYear');
Route::delete('accountings/kashutangpiutangstok/destroy/{id}', [KasHutangPiutangStokController::class, 'destroy'])
->name('accountings.kashutangpiutangstok.destroy');

//LAPORAN ARUS KAS
Route::get('accountings/aruskas', [ArusKasController::class, 'index'])
    ->name('accountings/aruskas');
Route::post('accountings/aruskas/store', [ArusKasController::class, 'store'])
    ->name('accountings/aruskas.store');
Route::put('accountings/aruskas/update/{id}', [ArusKasController::class, 'update'])
    ->name('accountings/aruskas.update');
Route::get('accountings/aruskas/data', [ArusKasController::class, 'data'])
    ->name('accountings/aruskas.data');
Route::get('accountings/aruskas/filter', [ArusKasController::class, 'filterData'])
    ->name('accountings/aruskas.filter');
Route::delete('accountings/aruskas/destroy/{id}', [ArusKasController::class, 'destroy'])
    ->name('accountings/aruskas.destroy');  

});

Route::middleware(['guest'])->group(function () {
    // Guest routes for login
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

    // Authenticated routes
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