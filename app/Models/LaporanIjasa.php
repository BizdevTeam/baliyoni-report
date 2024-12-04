<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanIjasa extends Model
{
    //
    use HasFactory;

    protected $table = 'laporan_ijasas';

    // Kolom yang dapat diisi menggunakan metode mass assignment
    protected $fillable = [
        'tanggal',      // Format bulan dan tahun (contoh: '11/2024')
        'jam',      
        'permasalahan',      
        'impact',      
        'troubleshooting',      
        'resolve_tanggal',      
        'resolve_jam',      
    ];
}
