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
        Schema::create('laporan_spis', function (Blueprint $table) {
            $table->id("id_spi");
            $table->string('bulan'); // Contoh: '08 / 2024'
            $table->string('judul');
            $table->string('aspek');
            $table->text('masalah')->nullable();
            $table->text('solusi')->nullable();
            $table->text('implementasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_spis');
    }
};
