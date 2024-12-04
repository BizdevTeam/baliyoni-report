<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanStok extends Model
{
    use HasFactory;

    protected $table = 'laporan_stoks';

    protected $fillable = ['bulan_tahun', 'stok'];
}