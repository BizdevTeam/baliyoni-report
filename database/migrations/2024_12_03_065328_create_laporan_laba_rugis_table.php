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
        Schema::create('laporan_laba_rugis', function (Blueprint $table) {
            $table->id('id_labarugi');
            $table->string('tanggal');
            $table->string('gambar')->nullable();
            $table->string('file_excel')->nullable();
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_laba_rugis');
    }
};
