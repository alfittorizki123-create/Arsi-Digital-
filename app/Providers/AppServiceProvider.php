<?php

namespace App\Providers;

use App\Models\Peminjaman;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $terlambatCount = Peminjaman::where(function ($q) {
                    $q->where('status', 'terlambat')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'dipinjam')
                             ->whereNotNull('tanggal_kembali_rencana')
                             ->where('tanggal_kembali_rencana', '<', now()->startOfDay());
                      });
                })->count();

                $view->with('terlambatCount', $terlambatCount);
            }
        });
    }
}
