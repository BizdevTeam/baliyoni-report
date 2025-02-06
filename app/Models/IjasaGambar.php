<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class IjasaGambar extends Model
{
    protected $table = 'ijasa_gambar'; // Nama tabel

    protected $primaryKey = 'id_ijasa_gambar'; // Primary key custom

    protected $fillable = ['bulan', 'gambar', 'keterangan'];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }
    protected $appends = ['bulan_formatted'];

}
