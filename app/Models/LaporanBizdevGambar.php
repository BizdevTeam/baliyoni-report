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

    protected $fillable = ['date', 'gambar', 'keterangan'];

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
