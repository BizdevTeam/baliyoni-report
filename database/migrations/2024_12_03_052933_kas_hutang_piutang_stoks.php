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
        Schema::create('kas_hutang_piutang_stoks', function (Blueprint $table) {
            $table->id();
                $table->string('bulan_tahun');
                $table->integer('kas');
                $table->integer('hutang');
                $table->integer('piutang');
                $table->integer('stok');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_hutang_piutang_stoks');
    }
};
