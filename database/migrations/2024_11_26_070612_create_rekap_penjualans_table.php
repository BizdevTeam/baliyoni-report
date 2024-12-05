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
        Schema::create('laporan_sakits', function (Blueprint $table) {
            $table->id();
            $table->string('bulan_tahun')->unique(); // Pastikan tidak ada duplikasi
            $table->integer('total_sakit')->default(0); // Default value untuk menghindari nilai null
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_penjualans');
    }
};
