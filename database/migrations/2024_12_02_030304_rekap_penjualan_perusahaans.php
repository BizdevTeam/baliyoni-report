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
            $table->id('id_rpp');
            $table->string('bulan');
            $table->enum('perusahaan',[
                'PT. BALI UNGGUL SEJAHTERA',
                'CV. DANA RASA',
                'CV. LAGAAN SAKETI',
                'CV. BALI JAKTI INFORMATIK',
                'CV. BALI LINGGA KOMPUTER',
                'CV. ARTSOLUTION',
                'PT. BALI LINGGA SAKA GUMI',
                'CV. SAHABAT UTAMA',
                'CV. N & b NET ACCESS',
                'PT. ELKA SOLUTION NUSANTARA',
                'CV. ARINDAH',
                'ARFALINDO',
            ]);
            $table->bigInteger('total_penjualan');
            $table->timestamps();
            $table->unique(['bulan', 'perusahaan']);
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
