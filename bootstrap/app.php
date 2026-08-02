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
        // Exclude logout dari proteksi CSRF agar logout selalu berhasil walau sesi sudah kadaluarsa
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

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
        // Tangani jika sesi/token CSRF kadaluarsa (Error 419) agar otomatis redirect ke halaman Login tanpa layar error 419
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login');
            }
            return redirect()->route('login')->with('info', 'Sesi Anda telah berakhir, silakan login kembali.');
        });
    })
    ->create();
