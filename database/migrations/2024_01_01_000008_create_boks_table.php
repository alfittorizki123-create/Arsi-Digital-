<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_boks', 50);
            $table->integer('tahun')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('rak_id')->nullable()->constrained('raks')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index Performa Search & Filter
            $table->index(['unit_id', 'tahun'], 'idx_boks_unit_tahun');
            $table->index('nomor_boks', 'idx_boks_nomor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boks');
    }
};
