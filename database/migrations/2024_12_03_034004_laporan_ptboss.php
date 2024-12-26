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
            $table->string('bulan_tahun');
            $table->string('pekerjaan'); 
            $table->string('kondisi_bulan_lalu');
            $table->string('kondisi_bulan_ini'); 
            $table->string('update'); 
            $table->string('rencana_implementasi');
            $table->string('keterangan');
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
