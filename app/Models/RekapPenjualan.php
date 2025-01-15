<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualan extends Model
{
    use HasFactory;

    protected $table = 'rekap_penjualans';
    protected $primaryKey = 'id_rp'; // Primary key custom

    protected $fillable = ['bulan', 'total_penjualan'];
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m-Y');
    }

    // Menambahkan accessor untuk kas, hutang, piutang, stok dengan format Rp
    public function getTotalPenjualanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_penjualan, 0, ',', '.');
    }
}