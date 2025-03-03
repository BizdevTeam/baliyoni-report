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
        Schema::create('rekap_penjualan_perusahaans', function (Blueprint $table) {
            $table->id(); // ID otomatis dibuat dengan nama 'id'
            $table->string('tanggal'); // Bisa juga pakai date_format
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->bigInteger('total_penjualan');
            $table->timestamps();
            
            // Mencegah duplikasi data bulan dan perusahaan
            $table->unique(['tanggal', 'perusahaan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_penjualan_perusahaans');
    }
};
