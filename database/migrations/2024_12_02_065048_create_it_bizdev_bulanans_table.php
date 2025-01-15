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
        Schema::create('it_bizdev_bulanans', function (Blueprint $table) {
            $table->id('id_bizdevbulanan');
            $table->string('bulan');
            $table->string('judul');
            $table->timestamps();

            $table->unique(['bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_bizdev_bulanans');
    }
};
