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
        Schema::create('it_bizdev_datas', function (Blueprint $table) {
            $table->id('id_bizdevdata');
            $table->unsignedBigInteger('bizdevbulanan_id'); // Kolom foreign key
            $table->string('aplikasi');
            $table->text('kondisi_bulanlalu');
            $table->text('kondisi_bulanini');
            $table->text('update');
            $table->text('rencana_implementasi');
            $table->text('keterangan');
            $table->timestamps();

            // Definisi foreign key
            $table->foreign('bizdevbulanan_id')
                  ->references('id_bizdevbulanan')
                  ->on('it_bizdev_bulanans')
                  ->onDelete('cascade'); // Menghapus data terkait jika data bulanan dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_bizdev_data');
    }
};
