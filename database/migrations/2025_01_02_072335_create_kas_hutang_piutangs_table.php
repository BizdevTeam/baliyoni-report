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
        Schema::create('kas_hutang_piutangs', function (Blueprint $table) {
            $table->id('id_khps');
            $table->string('bulan');
            $table->bigInteger('kas');
            $table->bigInteger('hutang');
            $table->bigInteger('piutang');
            $table->bigInteger('stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_hutang_piutangs');
    }
};
