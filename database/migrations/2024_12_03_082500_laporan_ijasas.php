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
        Schema::create('laporan_ijasas', function (Blueprint $table) {
            $table->id();   
            $table->string('tanggal'); // Pastikan tidak ada duplikasi
            $table->string('jam'); // Pastikan tidak ada duplikasi
            $table->string('permasalahan'); // Pastikan tidak ada duplikasi
            $table->string('impact'); // Pastikan tidak ada duplikasi
            $table->string('troubleshooting'); // Pastikan tidak ada duplikasi
            $table->string('resolve_tanggal'); // Pastikan tidak ada duplikasi
            $table->string('resolve_jam'); // Pastikan tidak ada duplikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('laporan_ijasas');
    }
};
