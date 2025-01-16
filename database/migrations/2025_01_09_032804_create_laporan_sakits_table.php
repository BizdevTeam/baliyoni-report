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
        Schema::create('laporan_sakits', function (Blueprint $table) {
            $table->id('id_sakit');
            $table->string('bulan');
            $table->integer('total_sakit')->default(0);
            $table->string('nama');
            $table->timestamps();

            $table->unique(['nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_sakits');
    }
};
