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
        {
            Schema::create('laporan_samitras', function (Blueprint $table) {
                $table->id('id_samitra');
                $table->string('bulan');
                $table->string('total_pengiriman');
                $table->timestamps();

                $table->unique(['bulan']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_samitras');
    }
};
