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
use App\Http\Controllers\KasHutangPiutangStokController;
use App\Http\Controllers\ItMultimediaInstagramController;
use App\Http\Controllers\LaporanPembelianOutletController;
use App\Http\Controllers\LaporanPembelianHoldingController;
use App\Http\Controllers\LaporanPaketAdministrasiController;
use App\Http\Controllers\LaporanSamitraController;
use App\Http\Controllers\RekapPenjualanPerusahaanController;
use App\Http\Controllers\RekapPendapatanServisAspController;
use App\Http\Controllers\RekapPiutangServisAspController;
use App\Http\Controllers\LaporanSPIController;
use App\Http\Controllers\laporanSPITiController;
use App\Http\Controllers\LaporanDetransController;



Route::middleware(['web'])->group(function () {
    // Accounting

  Route::prefix('accounting')->group(function () {
    // Resourceful Routes
    Route::resource('labarugi', LaporanLabaRugiController::class);
    Route::resource('neraca', LaporanNeracaController::class);
    Route::resource('rasio', LaporanRasioController::class);
    Route::resource('taxplaning', LaporanTaxPlaningController::class);
    Route::resource('laporanppn', LaporanPpnController::class);

    // KAS, HUTANG, PIUTANG, STOK
    Route::get('kashutangpiutangstok', [KasHutangPiutangStokController::class, 'index'])
        ->name('accounting.kashutangpiutangstok');
    Route::post('kashutangpiutangstok/store', [KasHutangPiutangStokController::class, 'store'])
        ->name('accounting.kashutangpiutangstok.store');
    Route::put('kashutangpiutangstok/update/{id}', [KasHutangPiutangStokController::class, 'update'])
        ->name('accounting.kashutangpiutangstok.update');
    Route::get('kashutangpiutangstok/data', [KasHutangPiutangStokController::class, 'data'])
        ->name('accounting.kashutangpiutangstok.data');
    Route::get('kashutangpiutangstok/filter', [KasHutangPiutangStokController::class, 'filterData'])
        ->name('accounting.kashutangpiutangstok.filter');
    Route::delete('kashutangpiutangstok/destroy/{id}', [KasHutangPiutangStokController::class, 'destroy'])
        ->name('accounting.kashutangpiutangstok.destroy');

    // LAPORAN ARUS KAS
    Route::get('aruskas', [ArusKasController::class, 'index'])
        ->name('accounting.aruskas');
    Route::post('aruskas/store', [ArusKasController::class, 'store'])
        ->name('accounting.aruskas.store');
    Route::put('aruskas/update/{id}', [ArusKasController::class, 'update'])
        ->name('accounting.aruskas.update');
    Route::get('aruskas/data', [ArusKasController::class, 'data'])
        ->name('accounting.aruskas.data');
    Route::get('aruskas/filter', [ArusKasController::class, 'filterData'])
        ->name('accounting.aruskas.filter');
    Route::delete('aruskas/destroy/{id}', [ArusKasController::class, 'destroy'])
        ->name('accounting.aruskas.destroy');
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

        // Rekap Penjualan
        Route::get('rekappenjualan', [RekapPenjualanController::class, 'index'])
            ->name('marketings.rekappenjualan');
        Route::post('rekappenjualan/store', [RekapPenjualanController::class, 'store'])
            ->name('marketings.rekappenjualan.store');
        Route::put('rekappenjualan/update/{id}', [RekapPenjualanController::class, 'update'])
            ->name('marketings.rekappenjualan.update');
        Route::get('rekappenjualan/data', [RekapPenjualanController::class, 'data'])
            ->name('marketings.rekappenjualan.data');
        Route::get('rekappenjualan/filter', [RekapPenjualanController::class, 'filterData'])
            ->name('marketings.rekappenjualan.filterByYear');
        Route::delete('rekappenjualan/destroy/{id}', [RekapPenjualanController::class, 'destroy'])
            ->name('marketings.rekappenjualan.destroy');

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
    });



    //PROCUREMENT
    Route::prefix('procurements')->group(function () {
        // LAPORAN PEMBELIAN HOLDING
        Route::get('laporanpembelianholding', [LaporanPembelianHoldingController::class, 'index'])
            ->name('procurements.laporanpembelianholding');
        Route::post('laporanpembelianholding/store', [LaporanPembelianHoldingController::class, 'store'])
            ->name('procurements.laporanpembelianholding.store');
        Route::put('laporanpembelianholding/update/{id}', [LaporanPembelianHoldingController::class, 'update'])
            ->name('procurements.laporanpembelianholding.update');
        Route::get('laporanpembelianholding/data', [LaporanPembelianHoldingController::class, 'data'])
            ->name('procurements.laporanpembelianholding.data');
        Route::get('laporanpembelianholding/filter', [LaporanPembelianHoldingController::class, 'filterData'])
            ->name('procurements.laporanpembelianholding.filter');
        Route::delete('laporanpembelianholding/destroy/{id}', [LaporanPembelianHoldingController::class, 'destroy'])
            ->name('procurements.laporanpembelianholding.destroy');

        // LAPORAN STOK
        Route::get('laporanstok', [LaporanStokController::class, 'index'])
            ->name('procurements.laporanstok');
        Route::post('laporanstok/store', [LaporanStokController::class, 'store'])
            ->name('procurements.laporanstok.store');
        Route::put('laporanstok/update/{id}', [LaporanStokController::class, 'update'])
            ->name('procurements.laporanstok.update');
        Route::get('laporanstok/data', [LaporanStokController::class, 'data'])
            ->name('procurements.laporanstok.data');
        Route::get('laporanstok/filter', [LaporanStokController::class, 'filterData'])
            ->name('procurements.laporanstok.filter');
        Route::delete('laporanstok/destroy/{id}', [LaporanStokController::class, 'destroy'])
            ->name('procurements.laporanstok.destroy');

        // LAPORAN PEMBELIAN OUTLET
        Route::get('laporanpembelianoutlet', [LaporanPembelianOutletController::class, 'index'])
            ->name('procurements.laporanpembelianoutlet');
        Route::post('laporanpembelianoutlet/store', [LaporanPembelianOutletController::class, 'store'])
            ->name('procurements.laporanpembelianoutlet.store');
        Route::put('laporanpembelianoutlet/update/{id}', [LaporanPembelianOutletController::class, 'update'])
            ->name('procurements.laporanpembelianoutlet.update');
        Route::get('laporanpembelianoutlet/data', [LaporanPembelianOutletController::class, 'data'])
            ->name('procurements.laporanpembelianoutlet.data');
        Route::get('laporanpembelianoutlet/filter', [LaporanPembelianOutletController::class, 'filterData'])
            ->name('procurements.laporanpembelianoutlet.filter');
        Route::delete('laporanpembelianoutlet/destroy/{id}', [LaporanPembelianOutletController::class, 'destroy'])
            ->name('procurements.laporanpembelianoutlet.destroy');

        // LAPORAN NEGOSIASI
        Route::get('laporannegosiasi', [LaporanNegosiasiController::class, 'index'])
            ->name('procurements.laporannegosiasi');
        Route::post('laporannegosiasi/store', [LaporanNegosiasiController::class, 'store'])
            ->name('procurements.laporannegosiasi.store');
        Route::put('laporannegosiasi/update/{id}', [LaporanNegosiasiController::class, 'update'])
            ->name('procurements.laporannegosiasi.update');
        Route::get('laporannegosiasi/data', [LaporanNegosiasiController::class, 'data'])
            ->name('procurements.laporannegosiasi.data');
        Route::get('laporannegosiasi/filter', [LaporanNegosiasiController::class, 'filterData'])
            ->name('procurements.laporannegosiasi.filter');
        Route::delete('laporannegosiasi/destroy/{id}', [LaporanNegosiasiController::class, 'destroy'])
            ->name('procurements.laporannegosiasi.destroy');
    });

    Route::prefix('supports')->group(function () {
        // LAPORAN REKAP PENDAPATAN SERVIS ASP
        Route::get('rekappendapatanservisasp', [RekapPendapatanServisAspController::class, 'index'])
            ->name('supports.rekappendapatanservisasp');
        Route::post('rekappendapatanservisasp/store', [RekapPendapatanServisAspController::class, 'store'])
            ->name('supports.rekappendapatanservisasp.store');
        Route::put('rekappendapatanservisasp/update/{id}', [RekapPendapatanServisAspController::class, 'update'])
            ->name('supports.rekappendapatanservisasp.update');
        Route::get('rekappendapatanservisasp/data', [RekapPendapatanServisAspController::class, 'data'])
            ->name('supports.rekappendapatanservisasp.data');
        Route::get('rekappendapatanservisasp/filter', [RekapPendapatanServisAspController::class, 'filterData'])
            ->name('supports.rekappendapatanservisasp.filter');
        Route::delete('rekappendapatanservisasp/destroy/{id}', [RekapPendapatanServisAspController::class, 'destroy'])
            ->name('supports.rekappendapatanservisasp.destroy');
    
        // LAPORAN REKAP PIUTANG SERVIS ASP
        Route::get('rekappiutangservisasp', [RekapPiutangServisAspController::class, 'index'])
            ->name('supports.rekappiutangservisasp');
        Route::post('rekappiutangservisasp/store', [RekapPiutangServisAspController::class, 'store'])
            ->name('supports.rekappiutangservisasp.store');
        Route::put('rekappiutangservisasp/update/{id}', [RekapPiutangServisAspController::class, 'update'])
            ->name('supports.rekappiutangservisasp.update');
        Route::get('rekappiutangservisasp/data', [RekapPiutangServisAspController::class, 'data'])
            ->name('supports.rekappiutangservisasp.data');
        Route::get('rekappiutangservisasp/filter', [RekapPiutangServisAspController::class, 'filterData'])
            ->name('supports.rekappiutangservisasp.filter');
        Route::delete('rekappiutangservisasp/destroy/{id}', [RekapPiutangServisAspController::class, 'destroy'])
            ->name('supports.rekappiutangservisasp.destroy');
    
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
        ->name('hrga.laporanterlambat');

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
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->middleware('UserAccess:spi');
    Route::get('/logout', [SessionController::class, 'logout']);
});
