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
        Schema::create('laporan_terlambats', function (Blueprint $table) {
            $table->id('id_terlambat');
            $table->string('tanggal');
            $table->integer('total_terlambat')->default(0);
            $table->string('nama');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_terlambats');
    }
};
