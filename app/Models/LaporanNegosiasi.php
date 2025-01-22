<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanNegosiasi extends Model
{
    //
    use HasFactory;
    protected $table = 'laporan_negosiasis'; // Nama tabel

    protected $primaryKey = 'id_negosiasi'; // Primary key custom

    protected $fillable = ['bulan', 'total_negosiasi'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getTotalNegosiasiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_negosiasi, 0, ',', '.');
    }
}
