<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPtBos extends Model
{
    //
    use HasFactory;

    protected $table = 'laporan_ptboss';

    // Kolom yang dapat diisi menggunakan metode mass assignment
    protected $fillable = [
        'bulan_tahun',      // Format bulan dan tahun (contoh: '11/2024')
        'pekerjaan',      
        'kondisi_bulan_lalu',      
        'kondisi_bulan_ini',      
        'update',      
        'rencana_implementasi',      
        'keterangan',      
    ];
}
