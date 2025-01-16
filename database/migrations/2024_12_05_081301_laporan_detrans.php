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
        Schema::create('laporan_detrans', function (Blueprint $table) {
            $table->id('id_detrans');
            $table->string('bulan');
            $table->bigInteger('total_pengiriman'); // Default value untuk menghindari nilai null
            $table->timestamps();

            $table->unique(['bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::dropIfExists('laporan_detrans');
    }
};
