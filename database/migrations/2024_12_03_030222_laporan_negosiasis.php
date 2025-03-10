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
        Schema::create('laporan_negosiasis', function (Blueprint $table) {
            $table->id('id_negosiasi');
            $table->string('tanggal');
            $table->bigInteger('total_negosiasi');
            $table->timestamps();

            $table->unique(['tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_negosiasis');
    }
};
