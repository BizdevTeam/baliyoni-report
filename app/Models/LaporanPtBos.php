<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanPtBos extends Model
{
    use HasFactory;
    protected $table = 'laporan_ptbos'; // Nama tabel

    protected $primaryKey = 'id_ptbos'; // Primary key custom

    protected $fillable = ['bulan', 'pekerjaan', 'kondisi_bulanlalu', 'kondisi_bulanini', 'update', 'rencana_implementasi', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }
}
