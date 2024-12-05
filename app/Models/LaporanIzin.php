<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanIzin extends Model
{
    //
    use HasFactory;

    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'laporan_izins';

    // Kolom yang dapat diisi menggunakan metode mass assignment
    protected $fillable = [
        'bulan_tahun',      // Format bulan dan tahun (contoh: '11/2024')
        'total_izin',      // Total paket dalam nilai integer
        'nama',       // Keterangan tambahan (nullable)
    ];
}
