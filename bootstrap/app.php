<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware kustom: Pencatatan aktivitas pengguna
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\LogUserActivity::class,
        ]);

        // Redirect jika belum login
        $middleware->redirectGuestsTo('/login');

        // Redirect jika sudah login
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Konfigurasi exception handling
    })
    ->create();
