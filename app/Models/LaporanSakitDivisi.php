<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanSakitDivisi extends Model
{
    protected $table = 'laporan_sakit_divisis'; // Nama tabel

    protected $primaryKey = 'id_laporan_sakit_divisi'; // Primary key custom

    protected $fillable = ['tanggal', 'nama', 'divisi', 'total_sakit'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
