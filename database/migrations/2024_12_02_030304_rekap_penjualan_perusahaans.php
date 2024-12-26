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
            $table->id();
                $table->string('bulan_tahun');
                $table->string('perusahaan');
                $table->bigIntegerz('nilai_paket');
                $table->timestamps();

                $table->unique(['bulan_tahun', 'perusahaan']);

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
