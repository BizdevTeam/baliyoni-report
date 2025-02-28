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
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->id('id_aruskas');
            $table->string('tanggal');
            $table->bigInteger('kas_masuk');
            $table->bigInteger('kas_keluar');
            $table->timestamps();

            $table->unique(['tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arus_kas');
    }
};
