<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPerInstansi extends Model
{
    use HasFactory;

    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'laporan_per_instansis';

    // Kolom yang dapat diisi menggunakan metode mass assignment
    protected $fillable = [
        'bulan_tahun',      // Format bulan dan tahun (contoh: '11/2024')
        'keterangan',       // Keterangan tambahan (nullable)
        'instansi',          // Nama website (string)
        'nilai',         // Nilai paket dalam rupiah (integer)
    ];
}
