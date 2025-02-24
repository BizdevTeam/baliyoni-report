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
            $table->string('date');
            $table->enum('website',[
                'E - Katalog',
                'E - Katalog Luar Bali',
                'Balimall',
                'Siplah',
            ]);
            $table->bigInteger('total_paket');
            $table->timestamps();

            $table->unique(['date', 'website']);
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
