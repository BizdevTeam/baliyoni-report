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

    protected $fillable = ['tanggal', 'total_penjualan'];
    
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->format('d F Y');
    }

    // Menambahkan accessor untuk kas, hutang, piutang, stok dengan format Rp
    public function getTotalPenjualanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_penjualan, 0, ',', '.');
    }
}