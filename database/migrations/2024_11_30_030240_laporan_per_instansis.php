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
                'Provinsi',
                'Bangli',
                'Klungkung',
            ]);
            $table->bigInteger('nilai');
            $table->timestamps();

            $table->unique(['bulan', 'instansi']);
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
