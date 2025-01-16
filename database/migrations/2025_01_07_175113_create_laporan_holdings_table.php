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
            $table->id('id_holding');
            $table->string('bulan');
            $table->enum('perusahaan', [
                'PT. Baliyoni Saguna',
                'CV. ELKA MANDIRI',
                'PT. NABA TECHNOLOGY SOLUTIONS',
                'CV. BHIRMA TEKNIK',
                'PT. DWI SRIKANDI NUSANTARA'
            ]);
            $table->bigInteger('nilai');
            $table->timestamps();

            $table->unique(['bulan', 'perusahaan']);
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
