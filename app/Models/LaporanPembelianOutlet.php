<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanPembelianOutlet extends Model
{
    use HasFactory;

    protected $table = 'laporan_pembelian_outlets';

    protected $fillable = ['bulan_tahun', 'total_pembelian'];
}
