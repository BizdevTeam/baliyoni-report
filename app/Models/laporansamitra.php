<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class laporansamitra extends Model
{
    use HasFactory;

    protected $table = 'laporan_samitras';

    protected $fillable = ['bulan_tahun', 'total_pengiriman'];
}
