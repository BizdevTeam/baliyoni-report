<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualanPerusahaan extends Model
{
    use HasFactory;
    protected $table = 'rekap_penjualan_perusahaans';
    protected $primaryKey = 'id_rpp'; // Primary key custom
    protected $fillable = [
        'bulan', 
        'perusahaan',     
        'total_penjualan',        
    ];
    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getTotalPenjualanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_penjualan, 0, ',', '.');
    }
}


