<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arsip_id')->nullable()->constrained('arsips')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_file', 50)->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->timestamps();

            // Index Performa
            $table->index('arsip_id', 'idx_arsip_files_arsip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_files');
    }
};
