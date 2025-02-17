<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id(); // Laravel otomatis membuat ID dengan nama 'id'
            $table->string('nama_perusahaan')->unique();
            $table->timestamps();
        });

        // Insert daftar perusahaan ke dalam tabel
        DB::table('perusahaans')->insert([
            ['nama_perusahaan' => 'PT. Bali Unggul Sejahtera'],
            ['nama_perusahaan' => 'CV. Dana Rasa'],
            ['nama_perusahaan' => 'CV. Lagaan Saketi'],
            ['nama_perusahaan' => 'CV. Bali Jakti Informatik'],
            ['nama_perusahaan' => 'CV. Bali Lingga Komputer'],
            ['nama_perusahaan' => 'CV. Artsolution'],
            ['nama_perusahaan' => 'PT. Bali Lingga Saka Gumi'],
            ['nama_perusahaan' => 'CV. Sahabat Utama'],
            ['nama_perusahaan' => 'CV. N & B Net Access'],
            ['nama_perusahaan' => 'PT. Elka Solution Nusantara'],
            ['nama_perusahaan' => 'CV. Arindah'],
            ['nama_perusahaan' => 'Arfalindo'],
            ['nama_perusahaan' => 'PT. Arisma Smart Solution'],
            ['nama_perusahaan' => 'PT. Integrasi Jasa Nusantara'],
            ['nama_perusahaan' => 'CV. Elka Mandiri'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};
