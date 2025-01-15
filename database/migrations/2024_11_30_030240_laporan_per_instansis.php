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
        Schema::create('laporan_per_instansis', function (Blueprint $table) {
            $table->id('id_perinstansi');
                $table->string('bulan');
                $table->enum('instansi',[
                    'Badung',
                    'Denpasar',
                    'Tabanan',
                    'Gianyar',
                    'Karangasem',
                    'Bangli',
                    'Singaraja',
                    'Jembrana',
                    'Klungkung',
                    'Provinsi',
                ]);
                $table->bigInteger('nilai');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_per_instansis');
    }
};
