<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_bisnis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit')->unique();
            $table->timestamps();
        });
        DB::table('unit_bisnis')->insert([
            ['nama_unit' => 'E - Katalog'],
            ['nama_unit' => 'E - Katalog Luar Bali'],
            ['nama_unit' => 'Balimall'],
            ['nama_unit' => 'Siplah'],
            ['nama_unit' => 'Pengadaan Langsung'],
            ['nama_unit' => 'Digi Pay'],
            ['nama_unit' => 'Umall'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_bisnis');
    }
};
