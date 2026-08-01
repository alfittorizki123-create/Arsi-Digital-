<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam')->unique();
            $table->string('nama_peminjam');
            $table->string('nip_peminjam')->nullable();
            $table->string('instansi_peminjam')->nullable();
            $table->string('kontak_peminjam')->nullable();
            $table->text('keperluan')->nullable();
            $table->date('tanggal_pinjam');
            $table->date('tenggat_kembali')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index Performa Search & Filter
            $table->index(['status', 'tanggal_pinjam'], 'idx_peminjamen_status_tgl');
            $table->index('nama_peminjam', 'idx_peminjamen_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
