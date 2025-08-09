<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanTerlambatDivisi extends Model
{
    protected $table = 'laporan_terlambat_divisis'; // Nama tabel

    protected $primaryKey = 'id_laporan_terlambat_divisi'; // Primary key custom

    protected $fillable = ['tanggal', 'nama', 'divisi', 'total_terlambat'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
