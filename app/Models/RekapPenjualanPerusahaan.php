<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualanPerusahaan extends Model
{
    use HasFactory;
    protected $table = 'rekap_penjualan_perusahaans';

    protected $fillable = [
        'bulan_tahun', 
        'perusahaan',     
        'nilai_paket',        
    ];
    protected $casts = [
        'nilai_paket' => 'integer',
    ];
}


