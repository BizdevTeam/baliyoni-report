<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_terlambat_divisis', function (Blueprint $table) {
            $table->id('id_laporan_terlambat_divisi');
            $table->string('tanggal');
            $table->string('nama');
            $table->string('divisi');
            $table->integer('total_terlambat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_terlambat_divisis');
    }
};
