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
            $table->enum('tipe_arsip', ['rekap', 'detail'])->default('detail');
            $table->string('kode_klasifikasi', 50)->nullable();
            $table->string('nomor_arsip_berkas', 100)->nullable();
            $table->text('uraian_informasi_arsip')->nullable();
            $table->integer('kurun_waktu')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan', 20)->default('Berkas');
            $table->enum('tingkat_perkembangan', ['Asli', 'Copy', 'Asli/Copy'])->nullable();
            $table->string('nomor_boks', 50)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak'])->default('Baik');
            $table->enum('klasifikasi_keamanan', ['Terbuka', 'Terbatas', 'Rahasia'])->default('Terbuka');
            $table->enum('status', ['aktif', 'inaktif'])->default('aktif');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('jenis_pajak_id')->nullable()->constrained('jenis_pajaks')->nullOnDelete();
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