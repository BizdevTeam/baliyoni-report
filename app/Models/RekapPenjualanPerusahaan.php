<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualanPerusahaan extends Model
{
    use HasFactory;

    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'rekap_penjualan_perusahaans';

    protected $fillable = [
        'bulan_tahun', 
        'perusahaan',     
        'nilai',        
    ];
}


