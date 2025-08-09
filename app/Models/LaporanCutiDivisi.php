<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanCutiDivisi extends Model
{
    protected $table = 'laporan_cuti_divisis'; // Nama tabel

    protected $primaryKey = 'id_laporan_cuti_divisi'; // Primary key custom

    protected $fillable = ['tanggal', 'nama', 'divisi', 'total_cuti'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
