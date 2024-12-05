<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekapPiutangServisAsp extends Model
{
    use HasFactory;
    
    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'rekap_piutang_servis_asps';

    protected $fillable = [
        'bulan_tahun', 
        'pelaksana',     
        'nilai_piutang',        
    ];
    protected $casts = [
        'nilai_piutang' => 'integer',
    ];
}
