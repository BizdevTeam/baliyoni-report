<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualan extends Model
{
    use HasFactory;

    protected $table = 'rekap_penjualans';

    protected $fillable = ['bulan_tahun', 'total_penjualan'];
}