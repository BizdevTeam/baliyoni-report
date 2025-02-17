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
        Schema::create('laporan_holdings', function (Blueprint $table) {
            $table->id(); // ID otomatis dibuat dengan nama 'id'
            $table->string('bulan'); // Bisa juga pakai date_format
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->bigInteger('nilai');
            $table->timestamps();
            
            // Mencegah duplikasi data bulan dan perusahaan
            $table->unique(['bulan', 'perusahaan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_holdings');
    }
};
