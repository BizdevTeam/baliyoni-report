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
        Schema::create('laporan_ijasas', function (Blueprint $table) {
            $table->id('id_ijasa');
            $table->date('tanggal');
            $table->time('jam');
            $table->string('permasalahan');
            $table->string('impact');
            $table->string('troubleshooting');
            $table->date('resolve_tanggal');
            $table->time('resolve_jam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_ijasas');
    }
};
