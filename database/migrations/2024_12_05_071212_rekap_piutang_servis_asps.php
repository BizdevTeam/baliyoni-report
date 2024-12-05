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
        Schema::create('rekap_piutang_servis_asps', function (Blueprint $table) {
            $table->id();
                $table->string('bulan_tahun');
                $table->string('pelaksana');
                $table->integer('nilai_piutang');
                $table->timestamps();

                $table->unique(['bulan_tahun', 'pelaksana']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_piutang_servis_asps');
    }
};
