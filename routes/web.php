<?php

use App\Http\Controllers\ArsipController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('arsips', ArsipController::class);

Route::get('/laporan', function () {
    return view('laporan');
})->name('laporan');

Route::get('/pengaturan', function () {
    return view('pengaturan');
})->name('pengaturan');
