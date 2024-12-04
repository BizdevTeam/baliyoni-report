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
        Schema::create('laporan_tax_planings', function (Blueprint $table) {
            $table->id('id_taxplaning');
            $table->string('bulan');
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
        Schema::dropIfExists('laporan_tax_planings');
    }
};
