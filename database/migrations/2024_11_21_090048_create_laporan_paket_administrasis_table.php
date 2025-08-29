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
        Schema::create('laporan_paket_administrasis', function (Blueprint $table) {
            $table->id('id_laporanpaket');
            $table->date('tanggal'); // Tipe data diubah menjadi date untuk konsistensi

            // Langsung membuat foreign key, tanpa kolom 'website'
            $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis');
            
            $table->bigInteger('total_paket');
            $table->timestamps();

            // Aturan unique yang baru, menggunakan unit_bisnis_id
            $table->unique(['tanggal', 'unit_bisnis_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_paket_administrasis');
    }
};