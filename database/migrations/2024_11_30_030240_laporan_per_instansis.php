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
        Schema::create('laporan_per_instansis', function (Blueprint $table) {
            $table->id();
                $table->string('bulan_tahun');
                $table->string('keterangan')->nullable();
                $table->string('instansi');
                $table->integer('nilai');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_per_instansis');
    }
};
