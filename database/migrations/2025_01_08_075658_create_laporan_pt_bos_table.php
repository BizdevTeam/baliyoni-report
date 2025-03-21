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
            $table->date('tanggal');
            $table->text('pekerjaan');
            $table->text('kondisi_bulanlalu');
            $table->text('kondisi_bulanini');
            $table->text('update');
            $table->text('rencana_implementasi');
            $table->text('keterangan');
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
