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
        // Gunakan Schema::table untuk memodifikasi tabel yang sudah ada
        Schema::table('laporan_paket_administrasis', function (Blueprint $table) {
            // 1. Tambahkan kolom foreign key baru.
            // Dibuat nullable() sementara agar bisa diisi.
            // ditaruh setelah kolom 'tanggal'
            $table->foreignId('unit_bisnis_id')
                  ->nullable()
                  ->after('tanggal')
                  ->constrained('unit_bisnis');
        });

        // 2. Proses memindahkan data lama dari kolom 'website' ke 'unit_bisnis_id'
        // Ambil semua unit bisnis untuk dicocokkan
        $unitBisnis = DB::table('unit_bisnis')->pluck('id', 'nama_unit');

        // Update setiap baris di laporan_paket_administrasis
        DB::table('laporan_paket_administrasis')->orderBy('id_laporanpaket')->chunk(100, function ($laporan) use ($unitBisnis) {
            foreach ($laporan as $item) {
                // Jika nama website ada di daftar unit bisnis, update kolom barunya
                if (isset($unitBisnis[$item->website])) {
                    DB::table('laporan_paket_administrasis')
                        ->where('id_laporanpaket', $item->id_laporanpaket)
                        ->update(['unit_bisnis_id' => $unitBisnis[$item->website]]);
                }
            }
        });

        // 3. Setelah data dipindahkan, hapus kolom 'website' yang lama
        Schema::table('laporan_paket_administrasis', function (Blueprint $table) {
            $table->dropColumn('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Metode 'down' ini untuk mengembalikan perubahan jika migrasi di-rollback
        Schema::table('laporan_paket_administrasis', function (Blueprint $table) {
            // 1. Buat kembali kolom 'website'
            $table->enum('website', [
                'E - Katalog',
                'E - Katalog Luar Bali',
                'Balimall',
                'Siplah',
            ])->after('tanggal');
            
            // (Opsional) Anda bisa menambahkan logika untuk mengembalikan data jika diperlukan

            // 2. Hapus foreign key dan kolomnya
            $table->dropForeign(['unit_bisnis_id']);
            $table->dropColumn('unit_bisnis_id');
        });
    }
};