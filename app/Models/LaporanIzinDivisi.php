<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanIzinDivisi extends Model
{
    protected $table = 'laporan_izin_divisis'; // Nama tabel

    protected $primaryKey = 'id_laporan_sakit_divisi'; // Primary key default

    protected $fillable = ['tanggal', 'divisi', 'total_izin'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    } 
}
