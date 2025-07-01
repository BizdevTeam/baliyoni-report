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
        Schema::create('tax_plannings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('tanggal');
            $table->bigInteger('tax_planning');
            $table->bigInteger('total_penjualan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_plannings');
    }
};
