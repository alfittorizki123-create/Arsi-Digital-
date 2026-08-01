<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip_jenis_pajak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arsip_id')->constrained('arsips')->onDelete('cascade');
            $table->foreignId('jenis_pajak_id')->constrained('jenis_pajaks')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['arsip_id', 'jenis_pajak_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_jenis_pajak');
    }
};
