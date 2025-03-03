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
        Schema::create('rekap_piutang_servis_a_s_p_s', function (Blueprint $table) {
            $table->id('id_rpiutangsasp');
            $table->string('tanggal');
            $table->enum('pelaksana', [
                'CV. ARI DISTRIBUTION CENTER',
                'CV. BALIYONI COMPUTER',
                'PT. NABA TECHNOLOGY SOLUTIONS',
                'CV. ELKA MANDIRI (50%)-SAMITRA',
                'CV. ELKA MANDIRI (50%)-DETRAN'
            ]);
            $table->bigInteger('nilai_piutang');
            $table->timestamps();

            $table->unique(['tanggal', 'pelaksana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_piutang_servis_a_s_p_s');
    }
};
