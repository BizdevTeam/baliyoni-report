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
        Schema::create('laporan_ptbos', function (Blueprint $table) {
            $table->id('id_ptbos');
            $table->string('date');
            $table->string('pekerjaan');
            $table->string('kondisi_bulanlalu');
            $table->string('kondisi_bulanini');
            $table->string('update');
            $table->string('rencana_implementasi');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ptbos');
    }
};
