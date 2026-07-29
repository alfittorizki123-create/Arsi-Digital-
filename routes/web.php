<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\ArsipImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisPajakController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BoksController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/arsips/pilih-unit', [ArsipController::class, 'pilihUnitUpload'])->name('arsips.pilih_unit');
    Route::get('/arsips/import', [ArsipImportController::class, 'create'])->name('arsips.import');
    Route::post('/arsips/import/preview', [ArsipImportController::class, 'preview'])->name('arsips.import.preview');
    Route::post('/arsips/import/preview-ajax', [ArsipImportController::class, 'previewAjax'])->name('arsips.import.preview_ajax');
    Route::get('/arsips/import/preview/{token}', [ArsipImportController::class, 'showPreview'])->name('arsips.import.show_preview');
    Route::post('/arsips/import/confirm', [ArsipImportController::class, 'confirm'])->name('arsips.import.confirm');
    Route::post('/arsips/import/cancel', [ArsipImportController::class, 'cancel'])->name('arsips.import.cancel');

    Route::resource('arsips', ArsipController::class);
    Route::post('/arsips/upload-temp-file', [ArsipController::class, 'uploadTempFile'])->name('arsips.upload_temp_file');
    Route::post('/arsips/{arsip}/upload-file', [ArsipController::class, 'uploadSingleFile'])->name('arsips.upload_file');
    Route::delete('/arsip-files/{arsipFile}', [ArsipController::class, 'destroyFile'])->name('arsip-files.destroy');

    Route::post('/raks/{rak}/assign-boks', [RakController::class, 'assignBoks'])->name('raks.assign_boks');
    Route::resource('raks', RakController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('/boks', [BoksController::class, 'store'])->name('boks.store');
    Route::put('/boks/{boks}', [BoksController::class, 'update'])->name('boks.update');
    Route::delete('/boks/{boks}', [BoksController::class, 'destroy'])->name('boks.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/arsips/search', [PeminjamanController::class, 'searchArsip'])->name('peminjaman.search_arsip');
    Route::get('/peminjaman/arsips-by-unit', [PeminjamanController::class, 'arsipsByUnit'])->name('peminjaman.arsips_by_unit');
    Route::get('/peminjaman/{peminjaman}/json', [PeminjamanController::class, 'json'])->name('peminjaman.json');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::post('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');

    Route::post('/pengaturan/jenis-pajak', [JenisPajakController::class, 'store'])->name('jenis-pajak.store');
    Route::put('/pengaturan/jenis-pajak/{jenisPajak}', [JenisPajakController::class, 'update'])->name('jenis-pajak.update');
    Route::delete('/pengaturan/jenis-pajak/{jenisPajak}', [JenisPajakController::class, 'destroy'])->name('jenis-pajak.destroy');

    Route::post('/pengaturan/unit', [UnitController::class, 'store'])->name('unit.store');
    Route::put('/pengaturan/unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/pengaturan/unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
});
