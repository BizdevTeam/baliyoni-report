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
        Schema::create('laporan_izin_divisis', function (Blueprint $table) {
            $table->id('id_laporan_izin_divisi');
            $table->string('tanggal');
            $table->string('nama');
            $table->string('divisi');
            $table->integer('total_izin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_izin_divisis');
    }
};
