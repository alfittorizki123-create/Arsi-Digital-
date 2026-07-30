<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsips', function (Blueprint $table) {
            $table->index(['unit_id', 'status'], 'idx_arsips_unit_status');
            $table->index(['unit_id', 'kurun_waktu'], 'idx_arsips_unit_tahun');
            $table->index(['kurun_waktu', 'status'], 'idx_arsips_tahun_status');
            $table->index('kode_klasifikasi', 'idx_arsips_kode_klasifikasi');
            $table->index('nomor_arsip_berkas', 'idx_arsips_nomor_berkas');
            $table->index('nomor_boks', 'idx_arsips_nomor_boks');
            $table->index('bulan', 'idx_arsips_bulan');
            $table->index('tipe_arsip', 'idx_arsips_tipe');
        });

        Schema::table('boks', function (Blueprint $table) {
            $table->index(['unit_id', 'tahun'], 'idx_boks_unit_tahun');
            $table->index('nomor_boks', 'idx_boks_nomor');
        });

        Schema::table('arsip_files', function (Blueprint $table) {
            $table->index('arsip_id', 'idx_arsip_files_arsip_id');
        });
    }

    public function down(): void
    {
        Schema::table('arsips', function (Blueprint $table) {
            $table->dropIndex('idx_arsips_unit_status');
            $table->dropIndex('idx_arsips_unit_tahun');
            $table->dropIndex('idx_arsips_tahun_status');
            $table->dropIndex('idx_arsips_kode_klasifikasi');
            $table->dropIndex('idx_arsips_nomor_berkas');
            $table->dropIndex('idx_arsips_nomor_boks');
            $table->dropIndex('idx_arsips_bulan');
            $table->dropIndex('idx_arsips_tipe');
        });

        Schema::table('boks', function (Blueprint $table) {
            $table->dropIndex('idx_boks_unit_tahun');
            $table->dropIndex('idx_boks_nomor');
        });

        Schema::table('arsip_files', function (Blueprint $table) {
            $table->dropIndex('idx_arsip_files_arsip_id');
        });
    }
};
