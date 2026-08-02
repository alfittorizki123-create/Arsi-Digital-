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
