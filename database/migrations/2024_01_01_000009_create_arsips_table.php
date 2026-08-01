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
            $table->unsignedTinyInteger('bulan')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan', 20)->default('Berkas');
            $table->enum('tingkat_perkembangan', ['Asli', 'Copy', 'Asli/Copy'])->nullable();
            $table->string('nomor_boks', 50)->nullable();
            $table->foreignId('boks_id')->nullable()->constrained('boks')->nullOnDelete();
            $table->enum('kondisi', ['Baik', 'Rusak'])->default('Baik');
            $table->enum('klasifikasi_keamanan', ['Terbuka', 'Terbatas', 'Rahasia'])->default('Terbuka');
            $table->enum('status', ['aktif', 'inaktif'])->default('aktif');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('path_file')->nullable();
            $table->string('tipe_file')->nullable();
            $table->timestamps();

            // Index Performa Search & Filter
            $table->index(['unit_id', 'status'], 'idx_arsips_unit_status');
            $table->index(['unit_id', 'kurun_waktu'], 'idx_arsips_unit_tahun');
            $table->index(['kurun_waktu', 'status'], 'idx_arsips_tahun_status');
            $table->index('kode_klasifikasi', 'idx_arsips_kode_klasifikasi');
            $table->index('nomor_arsip_berkas', 'idx_arsips_nomor_berkas');
            $table->index('nomor_boks', 'idx_arsips_nomor_boks');
            $table->index('bulan', 'idx_arsips_bulan');
            $table->index('tipe_arsip', 'idx_arsips_tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
