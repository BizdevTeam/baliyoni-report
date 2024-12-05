<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanDetrans extends Model
{
    use HasFactory;

    protected $table = 'laporan_detrans';

    protected $fillable = ['bulan_tahun', 'total_pengiriman'];
}

