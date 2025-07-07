<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom 'website' dari ENUM menjadi STRING (VARCHAR)
        DB::statement("ALTER TABLE laporan_paket_administrasis MODIFY website VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (jika diperlukan saat rollback)
        DB::statement("ALTER TABLE laporan_paket_administrasis MODIFY website ENUM(
            'E - Katalog',
            'E - Katalog Luar Bali',
            'Balimall',
            'Siplah'
        ) NOT NULL");
    }
};

