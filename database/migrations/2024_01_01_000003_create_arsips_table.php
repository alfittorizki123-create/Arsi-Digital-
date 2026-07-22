<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_arsip')->unique();
            $table->foreignId('jenis_pajak_id')->constrained('jenis_pajaks')->restrictOnDelete();
            $table->string('nama_wajib_pajak');
            $table->year('tahun_arsip');
            $table->string('nomor_rak')->nullable();
            $table->enum('status', ['aktif', 'inaktif'])->default('aktif');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('path_file')->nullable();
            $table->string('tipe_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};