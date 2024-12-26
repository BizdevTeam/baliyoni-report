<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPerInstansi extends Model
{
    use HasFactory;
    protected $table = 'laporan_per_instansis';

    protected $fillable = [
        'bulan_tahun',      
        'instansi',          
        'nilai',         
    ];

protected $casts = [
    'nilai' => 'integer',
];

}