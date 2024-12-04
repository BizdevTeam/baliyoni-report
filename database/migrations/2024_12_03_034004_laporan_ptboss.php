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
        Schema::create('laporan_ptboss', function (Blueprint $table) {
            $table->id();   
            $table->string('bulan_tahun'); // Pastikan tidak ada duplikasi
            $table->string('pekerjaan'); // Pastikan tidak ada duplikasi
            $table->string('kondisi_bulan_lalu'); // Pastikan tidak ada duplikasi
            $table->string('kondisi_bulan_ini'); // Pastikan tidak ada duplikasi
            $table->string('update'); // Pastikan tidak ada duplikasi
            $table->string('rencana_implementasi'); // Pastikan tidak ada duplikasi
            $table->string('keterangan'); // Pastikan tidak ada duplikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('laporan_ptboss');
    }
};
