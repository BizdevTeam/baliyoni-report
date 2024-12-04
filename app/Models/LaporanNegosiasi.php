<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanNegosiasi extends Model
{
    use HasFactory;

    protected $table = 'laporan_negosiasis';

    protected $fillable = ['bulan_tahun', 'total_negosiasi'];
}