<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ], [], [
            'username' => 'username',
            'password' => 'kata sandi',
        ]);

        $throttleKey = 'login_' . $request->input('username');

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withErrors(['username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."])
                ->onlyInput('username');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            ActivityLogger::log('login', 'Auth', 'Login berhasil', [
                'username' => $request->input('username'),
            ], $request);

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withErrors(['username' => 'Username atau kata sandi salah.'])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        ActivityLogger::log('logout', 'Auth', 'Logout dari aplikasi', [], $request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
