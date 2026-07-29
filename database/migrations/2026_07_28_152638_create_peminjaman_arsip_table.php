<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_arsip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamen')->cascadeOnDelete();
            $table->foreignId('arsip_id')->constrained('arsips')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['peminjaman_id', 'arsip_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_arsip');
    }
};
