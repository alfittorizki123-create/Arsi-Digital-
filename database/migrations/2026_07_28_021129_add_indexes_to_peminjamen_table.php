<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->index('status');
            $table->index('nama_peminjam');
            $table->index('tanggal_pinjam');
            $table->index('tanggal_dikembalikan');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['nama_peminjam']);
            $table->dropIndex(['tanggal_pinjam']);
            $table->dropIndex(['tanggal_dikembalikan']);
        });
    }
};
