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
        Schema::create('laporan_izins', function (Blueprint $table) {
            $table->id();   
            $table->string('bulan_tahun')->unique(); 
            $table->integer('total_izin')->default(0); 
            $table->string('nama')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        
        Schema::dropIfExists('laporan_izins');
    }
};
