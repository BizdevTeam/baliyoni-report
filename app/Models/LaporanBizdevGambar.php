<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanBizdevGambar extends Model
{
    //
    protected $table = 'laporan_bizdev_gambar'; // Nama tabel

    protected $primaryKey = 'id_laporan_bizdev_gambar'; // Primary key custom

    protected $fillable = ['bulan', 'gambar', 'keterangan'];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }
}
