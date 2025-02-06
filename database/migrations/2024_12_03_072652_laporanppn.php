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
        Schema::create('laporan_ppns', function (Blueprint $table) {
            $table->id('id_laporanppn');
            $table->string('bulan');
            $table->string('thumbnail')->nullable();
            $table->string('file')->nullable();
            $table->string('keterangan');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ppns');
    }
};
