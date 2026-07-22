<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\ArsipImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisPajakController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/arsips/import', [ArsipImportController::class, 'create'])->name('arsips.import');
    Route::post('/arsips/import/preview', [ArsipImportController::class, 'preview'])->name('arsips.import.preview');
    Route::post('/arsips/import/confirm', [ArsipImportController::class, 'confirm'])->name('arsips.import.confirm');
    Route::post('/arsips/import/cancel', [ArsipImportController::class, 'cancel'])->name('arsips.import.cancel');

    Route::resource('arsips', ArsipController::class);

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');

    Route::post('/pengaturan/jenis-pajak', [JenisPajakController::class, 'store'])->name('jenis-pajak.store');
    Route::put('/pengaturan/jenis-pajak/{jenisPajak}', [JenisPajakController::class, 'update'])->name('jenis-pajak.update');
    Route::delete('/pengaturan/jenis-pajak/{jenisPajak}', [JenisPajakController::class, 'destroy'])->name('jenis-pajak.destroy');

    Route::post('/pengaturan/unit', [UnitController::class, 'store'])->name('unit.store');
    Route::put('/pengaturan/unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/pengaturan/unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
});
