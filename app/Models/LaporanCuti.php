<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanCuti extends Model
{
    use HasFactory;

    protected $table = 'laporan_cutis';

    protected $fillable = [
        'bulan_tahun',      // Format bulan dan tahun (contoh: '11/2024')
        'total_cuti',      // Total paket dalam nilai integer
        'nama',       // Keterangan tambahan (nullable)
    ];
}
