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
        // Mengubah kolom 'website' dari VARCHAR menjadi ENUM
        Schema::table('laporan_paket_administrasis', function (Blueprint $table) {
            $table->enum('website', [
                'E - Katalog',
                'E - Katalog Luar Bali',
                'Balimall',
                'Siplah',
                'Pengadaan Langsung', // Tambahkan semua nilai yang mungkin ada
                'Digi Pay',
                'Umall'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan kolom 'website' dari ENUM menjadi VARCHAR (jika di-rollback)
        Schema::table('laporan_paket_administrasis', function (Blueprint $table) {
            $table->string('website', 50)->change();
        });
    }
};