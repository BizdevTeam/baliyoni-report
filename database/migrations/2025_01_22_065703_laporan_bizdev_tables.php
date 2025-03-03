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
        Schema::create('laporan_bizdevs', function (Blueprint $table) {
            $table->id("id_bizdev");
            $table->string('tanggal'); // Contoh: '08 / 2024'
            $table->string('aplikasi');
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
        Schema::dropIfExists('laporan_bizdevs');
    }
};
