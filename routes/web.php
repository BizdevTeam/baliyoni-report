<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\LaporanPpnController;
use App\Http\Controllers\LaporanCutiController;
use App\Http\Controllers\LaporanIzinController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\StatusPaketController;
use App\Http\Controllers\ItBizdevDataController;
use App\Http\Controllers\LaporanIjasaController;
use App\Http\Controllers\LaporanPtBosController;
use App\Http\Controllers\LaporanRasioController;
use App\Http\Controllers\LaporanSakitController;
use App\Http\Controllers\LaporanNeracaController;
use App\Http\Controllers\RekapPenjualanController;
use App\Http\Controllers\ItBizdevBulananController;
use App\Http\Controllers\LaporanLabaRugiController;
use App\Http\Controllers\LaporanNegosiasiController;
use App\Http\Controllers\LaporanTerlambatController;
use App\Http\Controllers\LaporanTaxPlaningController;
use App\Http\Controllers\ItMultimediaTiktokController;
use App\Http\Controllers\LaporanPerInstansiController;
use App\Http\Controllers\ItMultimediaInstagramController;
use App\Http\Controllers\KHPSController;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\LaporanSamitraController;
use App\Http\Controllers\RekapPenjualanPerusahaanController;
use App\Http\Controllers\RekapPendapatanServisAspController;
use App\Http\Controllers\RekapPiutangServisAspController;
use App\Http\Controllers\LaporanSPIController;
use App\Http\Controllers\laporanSPITiController;
use App\Http\Controllers\LaporanDetransController;
use App\Http\Controllers\LaporanHoldingController;
use App\Http\Controllers\LaporanOutletController;

Route::middleware(['web'])->group(function () {
    // Accounting
    Route::prefix('accounting')->group(function () {
        Route::resource('labarugi', LaporanLabaRugiController::class);
        Route::resource('neraca', LaporanNeracaController::class);
        Route::resource('rasio', LaporanRasioController::class);
        Route::resource('taxplaning', LaporanTaxPlaningController::class);
        Route::resource('laporanppn', LaporanPpnController::class);
        Route::resource('khps', KHPSController::class);
        Route::post('khps/export-pdf', [KHPSController::class, 'exportPDF'])->name('accounting.khps.exportPDF');
        Route::get('khps/data', [KHPSController::class, 'getKashutangpiutangstokData'])->name('accounting.khps.data');
        Route::resource('aruskas', ArusKasController::class);
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
    Route::prefix('marketings')->group(function () {
        Route::resource('rekappenjualan', RekapPenjualanController::class);
        Route::post('rekappenjualan/export-pdf', [RekapPenjualanController::class, 'exportPDF'])
            ->name('marketings.rekappenjualan.exportPDF');
        Route::get('rekappenjualan/data', [RekapPenjualanController::class, 'getRekapPenjualanData'])
            ->name('marketings.rekappenjualan.data');
        Route::delete('rekappenjualan/{rp}', [RekapPenjualanController::class, 'destroy']);


        // Laporan Paket Administrasi
        Route::get('laporanpaketadministrasi', [LaporanPaketAdministrasiController::class, 'index'])
            ->name('marketings.laporanpaketadministrasi');
        Route::post('laporanpaketadministrasi/store', [LaporanPaketAdministrasiController::class, 'store'])
            ->name('marketings.laporanpaketadministrasi.store');
        Route::put('laporanpaketadministrasi/update/{id}', [LaporanPaketAdministrasiController::class, 'update'])
            ->name('marketings.laporanpaketadministrasi.update');
        Route::get('laporanpaketadministrasi/data', [LaporanPaketAdministrasiController::class, 'data'])
            ->name('marketings.laporanpaketadministrasi.data');
        Route::get('laporanpaketadministrasi/filter', [LaporanPaketAdministrasiController::class, 'filterData'])
            ->name('marketings.laporanpaketadministrasi.filter');
        Route::delete('laporanpaketadministrasi/destroy/{id}', [LaporanPaketAdministrasiController::class, 'destroy'])
            ->name('marketings.laporanpaketadministrasi.destroy');
        Route::get('marketings/laporanpaketadministrasi/chart-data',[LaporanPaketAdministrasiController::class, 'getChartData'])
            ->name('marketings.laporanpaketadministrasi.chartdata');
        Route::post('laporanpaketadministrasi/export-pdf', [LaporanPaketAdministrasiController::class, 'exportPDF'])
            ->name('marketings.laporanpaketadministrasi.exportPDF');

        // Status Paket
        Route::get('statuspaket', [StatusPaketController::class, 'index'])
            ->name('marketings.laporanstatuspaket');
        Route::post('statuspaket/store', [StatusPaketController::class, 'store'])
            ->name('marketings.laporanstatuspaket.store');
        Route::put('statuspaket/update/{id}', [StatusPaketController::class, 'update'])
            ->name('marketings.laporanstatuspaket.update');
        Route::get('statuspaket/data', [StatusPaketController::class, 'data'])
            ->name('marketings.laporanstatuspaket.data');
        Route::get('statuspaket/filter', [StatusPaketController::class, 'filterData'])
            ->name('marketings.laporanstatuspaket.filter');
        Route::delete('statuspaket/destroy/{id}', [StatusPaketController::class, 'destroy'])
            ->name('marketings.laporanstatuspaket.destroy');
            Route::post('statuspaket/export-pdf', [StatusPaketController::class, 'exportPDF'])
            ->name('marketings.statuspaket.exportPDF');

        // Laporan Per Instansi
        Route::get('laporanperinstansi', [LaporanPerInstansiController::class, 'index'])
            ->name('marketings.laporanperinstansi');
        Route::post('laporanperinstansi/store', [LaporanPerInstansiController::class, 'store'])
            ->name('marketings.laporanperinstansi.store');
        Route::put('laporanperinstansi/update/{id}', [LaporanPerInstansiController::class, 'update'])
            ->name('marketings.laporanperinstansi.update');
        Route::get('laporanperinstansi/data', [LaporanPerInstansiController::class, 'data'])
            ->name('marketings.laporanperinstansi.data');
        Route::get('laporanperinstansi/filter', [LaporanPerInstansiController::class, 'filterData'])
            ->name('marketings.laporanperinstansi.filter');
        Route::delete('laporanperinstansi/destroy/{id}', [LaporanPerInstansiController::class, 'destroy'])
            ->name('marketings.laporanperinstansi.destroy');
        Route::post('laporanperinstansi/export-pdf', [LaporanPerInstansiController::class, 'exportPDF'])
            ->name('marketings.laporanperinstansi.exportPDF');

        // Rekap Penjualan Perusahaan
        Route::get('rekappenjualanperusahaan', [RekapPenjualanPerusahaanController::class, 'index'])
            ->name('marketings.rekappenjualanperusahaan');
        Route::post('rekappenjualanperusahaan/store', [RekapPenjualanPerusahaanController::class, 'store'])
            ->name('marketings.rekappenjualanperusahaan.store');
        Route::put('rekappenjualanperusahaan/update/{id}', [RekapPenjualanPerusahaanController::class, 'update'])
            ->name('marketings.rekappenjualanperusahaan.update');
        Route::get('rekappenjualanperusahaan/data', [RekapPenjualanPerusahaanController::class, 'data'])
            ->name('marketings.rekappenjualanperusahaan.data');
        Route::get('rekappenjualanperusahaan/filter', [RekapPenjualanPerusahaanController::class, 'filterData'])
            ->name('marketings.rekappenjualanperusahaan.filter');
        Route::delete('rekappenjualanperusahaan/destroy/{id}', [RekapPenjualanPerusahaanController::class, 'destroy'])
            ->name('marketings.rekappenjualanperusahaan.destroy');
        Route::post('rekappenjualanperusahaan/export-pdf', [RekapPenjualanPerusahaanController::class, 'exportPDF'])
            ->name('marketings.rekappenjualanperusahaan.exportPDF');

    });



    // PROCUREMENT
    Route::prefix('procurements')->group(function () {
        Route::resource('laporanholding', LaporanHoldingController::class);
        Route::resource('laporanstok', LaporanStokController::class);
        Route::resource('laporanoutlet', LaporanOutletController::class);
        Route::resource('laporannegosiasi', LaporanNegosiasiController::class);
    });

    Route::prefix('supports')->group(function () {
        Route::resource('rpsasp', RekapPendapatanServisAspController::class);
        Route::resource('rpiutangsasp', RekapPiutangServisAspController::class);
    
        // RUTE REKAP PENJUALAN
        Route::get('laporandetrans', [LaporanDetransController::class, 'index'])
            ->name('supports.laporandetrans');
        Route::post('laporandetrans/store', [LaporanDetransController::class, 'store'])
            ->name('supports.laporandetrans.store');
        Route::put('laporandetrans/update/{id}', [LaporanDetransController::class, 'update'])
            ->name('supports.laporandetrans.update');
        Route::get('laporandetrans/data', [LaporanDetransController::class, 'data'])
            ->name('supports.laporandetrans.data');
        Route::get('laporandetrans/filter', [LaporanDetransController::class, 'filterData'])
            ->name('supports.laporandetrans.filterByYear');
        Route::delete('laporandetrans/destroy/{id}', [LaporanDetransController::class, 'destroy'])
            ->name('supports.laporandetrans.destroy');
    
        Route::get('laporansamitra', [LaporanSamitraController::class, 'index'])
            ->name('supports.laporansamitra');
        Route::post('laporansamitra/store', [LaporanSamitraController::class, 'store'])
            ->name('supports.laporansamitra.store');
        Route::put('laporansamitra/update/{id}', [LaporanSamitraController::class, 'update'])
            ->name('supports.laporansamitra.update');
        Route::get('laporansamitra/data', [LaporanSamitraController::class, 'data'])
            ->name('supports.laporansamitra.data');
        Route::get('laporansamitra/filter', [LaporanSamitraController::class, 'filterData'])
            ->name('supports.laporansamitra.filterByYear');
        Route::delete('laporansamitra/destroy/{id}', [LaporanSamitraController::class, 'destroy'])
            ->name('supports.laporansamitra.destroy');
    });

    Route::prefix('hrga')->group(function () {
        Route::resource('laporantelat', LaporanTerlambatController::class);
        Route::resource('laporancuti', LaporanCutiController::class);
        Route::resource('laporanizin', LaporanIzinController::class);
        Route::resource('laporansakit', LaporanSakitController::class);
        Route::resource('laporanptbos', LaporanPtBosController::class);
        Route::resource('laporanijasa', LaporanIjasaController::class);
    //Route untuk laporan ijasa hrga
    });
});


Route::middleware(['web'])->group(function () {

    //spi
    Route::resource('laporanspi', LaporanSPIController::class);
    route::resource("laporanspiti", controller: laporanSPITiController::class);
});

Route::middleware(['guest'])->group(function () {
    // Guest routes for login
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('layouts.admin')->middleware('UserAccess:superadmin');
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->name('layouts.marketing')->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->name('layouts.it')->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->name('layouts.accounting')->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->name('layouts.procurement')->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->name('layouts.hrga')->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->name('layouts.spi')->middleware('UserAccess:spi');
    Route::get('/admin/support', [AdminController::class, 'support'])->name('layouts.support')->middleware('UserAccess:support');
    Route::get('/logout', [SessionController::class, 'logout'])->name('logout');
});
