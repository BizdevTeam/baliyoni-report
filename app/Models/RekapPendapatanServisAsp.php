<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekapPendapatanServisAsp extends Model
    {
        use HasFactory;
    
        // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
        protected $table = 'rekap_pendapatan_servis_asps';
    
        protected $fillable = [
            'bulan_tahun', 
            'perusahaan',     
            'nilai_pendapatan',        
        ];
        protected $casts = [
            'nilai_pendapatan' => 'integer',
        ];
}

