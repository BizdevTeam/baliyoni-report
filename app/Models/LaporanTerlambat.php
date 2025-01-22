<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanTerlambat extends Model
{
    use HasFactory;
    protected $table = 'laporan_terlambats'; // Nama tabel

    protected $primaryKey = 'id_terlambat'; // Primary key custom

    protected $fillable = ['bulan', 'total_terlambat', 'nama'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }
}
