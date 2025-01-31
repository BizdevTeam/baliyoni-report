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
use App\Http\Controllers\LaporanIjasaController;
use App\Http\Controllers\LaporanPtBosController;
use App\Http\Controllers\LaporanRasioController;
use App\Http\Controllers\LaporanSakitController;
use App\Http\Controllers\LaporanNeracaController;
use App\Http\Controllers\RekapPenjualanController;
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
use App\Http\Controllers\LaporanBizdevController;
use App\Http\Controllers\QuestionController;

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
        Route::post('aruskas/export-pdf', [ArusKasController::class, 'exportPDF'])
        ->name('accounting.aruskas.exportPDF');
    });

    // // IT
    // // Route::prefix('it')->group(function () {
   
    // //     Route::resource('bizdevbulanan', ItBizdevBulananController::class);
    // //     Route::prefix('bizdevbulanan/{bizdevbulanan_id}')->group(function () {
    // //         Route::get('bizdevdata', [ItBizdevDataController::class, 'index'])->name('bizdevdata.index');
    // //         Route::get('bizdevdata/create', [ItBizdevDataController::class, 'create'])->name('bizdevdata.create');
    // //         Route::post('bizdevdata/store', [ItBizdevDataController::class, 'store'])->name('bizdevdata.store');
    // //         Route::get('bizdevdata/{id_bizdevdata}/edit', [ItBizdevDataController::class, 'edit'])->name('bizdevdata.edit');
    // //         Route::put('bizdevdata/{id_bizdevdata}/update', [ItBizdevDataController::class, 'update'])->name('bizdevdata.update');
    // //         Route::delete('bizdevdata/{id_bizdevdata}/destroy', [ItBizdevDataController::class, 'destroy'])->name('bizdevdata.destroy');
    // //         Route::post('bizdevdata/export-pdf', [ItBizdevDataController::class, 'exportPDF'])
    // //         ->name('it.bizdevdata.exportPDF');
    // //     });
    // });


    // Menampilkan halaman laporan paket administrasi
    Route::prefix('marketings')->group(function () {
        Route::resource('rekappenjualan', RekapPenjualanController::class);
        Route::post('rekappenjualan/export-pdf', [RekapPenjualanController::class, 'exportPDF'])
            ->name('marketings.rekappenjualan.exportPDF');
        Route::get('rekappenjualan/data', [RekapPenjualanController::class, 'getRekapPenjualanData'])
            ->name('marketings.rekappenjualan.data');
        Route::delete('rekappenjualan/{rp}', [RekapPenjualanController::class, 'destroy']);

        Route::resource('rekappenjualanperusahaan', RekapPenjualanPerusahaanController::class);
        Route::post('rekappenjualanperusahaan/export-pdf', [RekapPenjualanPerusahaanController::class, 'exportPDF'])
            ->name('marketings.rekappenjualanperusahaan.exportPDF');
        Route::get('rekappenjualanperusahaan/data', [RekapPenjualanPerusahaanController::class, 'getRekapPenjualaPerusahaannData'])
            ->name('marketings.rekappenjualanperusahaan.data');
        Route::delete('rekappenjualanperusahaan/{rp}', [RekapPenjualanPerusahaanController::class, 'destroy']);

        Route::resource('laporanpaketadministrasi', LaporanPaketAdministrasiController::class);
        Route::post('laporanpaketadministrasi/export-pdf', [LaporanPaketAdministrasiController::class, 'exportPDF'])
            ->name('marketings.laporanpaketadministrasi.exportPDF');
        Route::get('laporanpaketadministrasi/chart-data', [LaporanPaketAdministrasiController::class, 'getChartData'])
            ->name('marketings.laporanpaketadministrasi.getChartData');
        Route::delete('laporanpaketadministrasi/{rp}', [LaporanPaketAdministrasiController::class, 'destroy']);

        Route::resource('statuspaket', StatusPaketController::class);
        Route::post('statuspaket/export-pdf', [StatusPaketController::class, 'exportPDF'])
            ->name('marketings.statuspaket.exportPDF');
        Route::get('statuspaket/data', [StatusPaketController::class, 'getStatusPaketData'])
            ->name('marketings.statuspaket.data');
        Route::delete('statuspaket/{rp}', [StatusPaketController::class, 'destroy']);

        Route::resource('laporanperinstansi', LaporanPerInstansiController::class);
        Route::post('laporanperinstansi/export-pdf', [LaporanPerInstansiController::class, 'exportPDF'])
            ->name('marketings.laporanperinstansi.exportPDF');
        Route::get('laporanperinstansi/data', [LaporanPerInstansiController::class, 'getLaporanPerinstansiData'])
            ->name('marketings.laporanperinstansi.data');
        Route::delete('laporanperinstansi/{rp}', [LaporanPerInstansiController::class, 'destroy']);

    });

    Route::prefix('it')->group(function () {
        Route::resource('laporanbizdev', LaporanBizdevController::class);
        Route::post('laporanbizdev/export-pdf', [LaporanBizdevController::class, 'exportPDF'])
            ->name('it.laporanbizdev.exportPDF');
        Route::resource('instagram', ItMultimediaInstagramController::class);
        Route::resource('tiktok', ItMultimediaTiktokController::class);
        Route::post('tiktok/export-pdf', [ItMultimediaTiktokController::class, 'exportPDF'])
            ->name('it.tiktok.exportPDF');
    });
    // PROCUREMENT
    Route::prefix('procurements')->group(function () {
        
        Route::resource('laporanholding', LaporanHoldingController::class);
        Route::get('laporanholding/data', [LaporanHoldingController::class, 'getLaporanStokData'])
            ->name('procurements.laporanholding.data');
        Route::post('laporanholding/export-pdf', [LaporanHoldingController::class, 'exportPDF'])
            ->name('procurements.laporanholding.exportPDF');
        Route::delete('laporanholding/{rp}', [LaporanHoldingController::class, 'destroy']);


        Route::resource('laporanstok', LaporanStokController::class);
        Route::get('laporanstok/data', [LaporanStokController::class, 'getLaporanHoldingData'])
            ->name('procurements.laporanstok.data');
        Route::post('laporanstok/export-pdf', [LaporanStokController::class, 'exportPDF'])
            ->name('procurements.laporanstok.exportPDF');
        Route::delete('laporanstok/{rp}', [LaporanStokController::class, 'destroy']);


        Route::resource('laporanoutlet', LaporanOutletController::class);
        Route::post('laporanoutlet/export-pdf', [LaporanOutletController::class, 'exportPDF'])
        ->name('procurements.laporanoutlet.exportPDF');

        Route::resource('laporannegosiasi', LaporanNegosiasiController::class);
        Route::post('laporannegosiasi/export-pdf', [LaporanNegosiasiController::class, 'exportPDF'])
        ->name('procurements.laporannegosiasi.exportPDF');
        Route::get('laporannegosiasi/data', [LaporanNegosiasiController::class, 'getLaporanNegosiasiData'])
        ->name('procurements.laporannegosiasi.data');

    });

    Route::prefix('supports')->group(function () {
        Route::resource('rekappendapatanservisasp', RekapPendapatanServisAspController::class);
        Route::post('rekappendapatanservisasp/export-pdf', [RekapPendapatanServisAspController::class, 'exportPDF'])
            ->name('supports.rekappendapatanservisasp.exportPDF');

        Route::resource('rekappiutangservisasp', RekapPiutangServisAspController::class);
        Route::post('rekappiutangservisasp/export-pdf', [RekapPiutangServisAspController::class, 'exportPDF'])
        ->name('supports.rekappiutangservisasp.exportPDF');

        Route::resource('laporansamitra', LaporanSamitraController::class);
        Route::post('laporansamitra/export-pdf', [LaporanSamitraController::class, 'exportPDF'])
            ->name('supports.laporansamitra.exportPDF');
        Route::get('laporansamitra/data', [LaporanSamitraController::class, 'getLaporanSamitraData'])
            ->name('supports.laporansamitra.data');
        Route::delete('laporansamitra/{rp}', [LaporanSamitraController::class, 'destroy']);

        Route::resource('laporandetrans', LaporanDetransController::class);
        Route::post('laporandetrans/export-pdf', [LaporanDetransController::class, 'exportPDF'])
            ->name('supports.laporandetrans.exportPDF');
        Route::get('laporandetrans/data', [LaporanDetransController::class, 'getLaporanSamitraData'])
            ->name('supports.laporandetrans.data');
        Route::delete('laporandetrans/{rp}', [LaporanDetransController::class, 'destroy']);
    });

    Route::prefix('hrga')->group(function () {
        Route::resource('laporanterlambat', LaporanTerlambatController::class);
        Route::post('laporanterlambat/export-pdf', [LaporanTerlambatController::class, 'exportPDF'])
        ->name('hrga.laporanterlambat.exportPDF');

        Route::resource('laporancuti', LaporanCutiController::class);
        Route::post('laporancuti/export-pdf', [LaporanCutiController::class, 'exportPDF'])
        ->name('hrga.laporancuti.exportPDF');
        Route::delete('laporancuti/{rp}', [LaporanCutiController::class, 'destroy']);


        Route::resource('laporanizin', LaporanIzinController::class);
        Route::post('laporanizin/export-pdf', [LaporanIzinController::class, 'exportPDF'])
        ->name('hrga.laporanizin.exportPDF');

        Route::resource('laporansakit', LaporanSakitController::class);
        Route::post('laporansakit/export-pdf', [LaporanSakitController::class, 'exportPDF'])
        ->name('hrga.laporansakit.exportPDF');
        Route::delete('laporansakit/{rp}', [LaporanSakitController::class, 'destroy']);


        Route::resource('laporanptbos', LaporanPtBosController::class);
        Route::post('laporanptbos/export-pdf', [LaporanPtBosController::class, 'exportPDF'])
            ->name('hrga.laporanptbos.exportPDF');
        Route::post('laporanptbos/table', [LaporanPtBosController::class, 'table'])
            ->name('hrga.laporanptbos.table');

        Route::resource('laporanijasa', LaporanIjasaController::class);
        Route::post('laporanijasa/export-pdf', [LaporanIjasaController::class, 'exportPDF'])
            ->name('hrga.laporanijasa.exportPDF');
    });

    Route::prefix('spi')->group(function () {
        Route::resource('laporanspi', LaporanSPIController::class);
        Route::post('laporanspi/export-pdf', [LaporanSPIController::class, 'exportPDF'])
            ->name('spi.laporanspi.exportPDF');
        route::resource("laporanspiti", controller: laporanSPITiController::class);
        Route::post('laporanspiti/export-pdf', [laporanSPITiController::class, 'exportPDF'])
            ->name('spi.laporanspiti.exportPDF');
    });

    Route::prefix('ask')->group(function () {
        Route::resource('questions', QuestionController::class); // CRUD lengkap
        Route::post('/questions/{id}/answer', [QuestionController::class, 'storeAnswer'])->name('answers.store'); // Rute tambahan untuk jawaban
        Route::put('/questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
        Route::put('/answers/{id}', [QuestionController::class, 'updateAnswer'])->name('answers.update');
        Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        Route::delete('/answers/{id}', [QuestionController::class, 'destroyAnswer'])->name('answers.destroy');
        
    });

});



// Route untuk menampilkan view

// Route untuk mengambil data chart
Route::get('/admin/chart-data', [LaporanPaketAdministrasiController::class, 'showChart'])->name('admin.chart.data');
Route::get('/adminpenjualan/chart-data', [RekapPenjualanController::class, 'showChart'])->name('adminpenjualan.chart.data');
Route::get('/adminpp/chart-data', [RekapPenjualanPerusahaanController::class, 'showChart'])->name('adminpp.chart.data');
Route::get('/adminstatuspaket/chart-data', [StatusPaketController::class, 'showChart'])->name('adminstatuspaket.chart.data');
Route::get('/adminperinstansi/chart-data', [LaporanPerInstansiController::class, 'showChart'])->name('adminperinstansi.chart.data');
Route::get('/adminholding/chart-data', [LaporanHoldingController::class, 'showChart'])->name('adminholding.chart.data');
Route::get('/adminstok/chart-data', [LaporanStokController::class, 'showChart'])->name('adminstok.chart.data');
Route::get('/adminoutlet/chart-data', [LaporanOutletController::class, 'showChart'])->name('adminoutlet.chart.data');
Route::get('/adminnegosiasi/chart-data', [LaporanNegosiasiController::class, 'showChart'])->name('adminnegosiasi.chart.data');
Route::get( '/adminpendapatanpengirimanbali/chart-data', [LaporanSamitraController::class, 'showChart'])->name('adminpendapatanpengirimanbali.chart.data');
Route::get( '/adminpendapatanpengirimanluarbali/chart-data', [LaporanDetransController::class, 'showChart'])->name('adminpendapatanpengirimanluarbali.chart.data');
Route::get( '/adminpendapatanservisasp/chart-data', [RekapPendapatanServisAspController::class, 'showChart'])->name('adminpendapatanservisasp.chart.data');
Route::get( '/adminpiutangservisasp/chart-data', [RekapPiutangServisAspController::class, 'showChart'])->name('adminpiutangservisasp.chart.data');

Route::get('/adminsakit/chart-data', [LaporanSakitController::class, 'showChart'])->name('adminsakit.chart.data');
Route::get('/adminizin/chart-data', [LaporanIzinController::class, 'showChart'])->name('adminizin.chart.data');
Route::get('/admincuti/chart-data', [LaporanCutiController::class, 'showChart'])->name('admincuti.chart.data');
Route::get('adminterlambat/chart-data', [LaporanTerlambatController::class, 'showChart'])->name('adminterlambat.chart.data');

Route::get('/adminkhps/chart-data', [KHPSController::class, 'showChart'])->name('adminkhps.chart.data');
Route::get('/adminak/chart-data', [ArusKasController::class, 'showChart'])->name('adminak.chart.data');
Route::get('/adminptbos', [LaporanPtBosController::class, 'adminView'])->name('adminptbos.admin');
Route::get('/adminijasa', [LaporanIjasaController::class, 'adminView'])->name('adminptbos.admin');
Route::get('/adminbizdev', [LaporanBizdevController::class, 'adminView'])->name('adminptbos.admin');



Route::middleware(['guest'])->group(function () {
    // Guest routes for login
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('layouts.admin')->middleware('UserAccess:superadmin');
    Route::get('/admin/laporan-paket-administrasi', [LaporanPaketAdministrasiController::class, 'index'])->name('admin.laporan.paket.administrasi');
    Route::get('/admin/marketing', [AdminController::class, 'marketing'])->name('layouts.marketing')->middleware('UserAccess:marketing');
    Route::get('/admin/it', [AdminController::class, 'it'])->name('layouts.it')->middleware('UserAccess:it');
    Route::get('/admin/accounting', [AdminController::class, 'accounting'])->name('layouts.accounting')->middleware('UserAccess:accounting');
    Route::get('/admin/procurement', [AdminController::class, 'procurement'])->name('layouts.procurement')->middleware('UserAccess:procurement');
    Route::get('/admin/hrga', [AdminController::class, 'hrga'])->name('layouts.hrga')->middleware('UserAccess:hrga');
    Route::get('/admin/spi', [AdminController::class, 'spi'])->name('layouts.spi')->middleware('UserAccess:spi');
    Route::get('/admin/support', [AdminController::class, 'support'])->name('layouts.support')->middleware('UserAccess:support');
    Route::get('/logout', [SessionController::class, 'logout'])->name('logout');

});

