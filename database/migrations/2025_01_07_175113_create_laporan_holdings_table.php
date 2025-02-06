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
                'CV. Elka Mandiri',
                'PT. Naba Technology Solutions',
                'CV. Bhirma Teknik',
                'PT. Dwi Srikandi Nusantara',
                'PT. Dwi Srikandi Indonesia'

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
