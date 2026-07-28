<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip_jenis_pajak', function (Blueprint $table) {
            $table->unique(['arsip_id', 'jenis_pajak_id']);
        });
    }

    public function down(): void
    {
        Schema::table('arsip_jenis_pajak', function (Blueprint $table) {
            $table->dropUnique(['arsip_id', 'jenis_pajak_id']);
        });
    }
};
