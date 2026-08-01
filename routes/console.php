<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes (Laravel 12 Style)
|--------------------------------------------------------------------------
|
| Tempat mendaftarkan perintah console Closure dan jadwal tugas.
| Command class seperti CleanTempFiles otomatis ter-discover.
|
*/

// Jadwal pembersihan file temporary setiap hari jam 02:00 pagi
Schedule::command('arsip:clean-temp-files')->daily()->at('02:00');

Artisan::command('fix:boks-units', function () {
    $count = 0;
    foreach (\App\Models\Boks::whereNull('unit_id')->get() as $boks) {
        $arsipUnitId = $boks->arsips()->whereNotNull('unit_id')->value('unit_id');
        if ($arsipUnitId) {
            $boks->update(['unit_id' => $arsipUnitId]);
            $count++;
        }
    }
    $this->info("Berhasil memperbaiki {$count} boks unit_id.");
});
