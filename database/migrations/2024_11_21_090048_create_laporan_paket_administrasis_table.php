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
            $table->id();
            $table->string('bulan_tahun');
            $table->string('website');
            $table->bigInteger('paket_rp');
            $table->timestamps();
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
