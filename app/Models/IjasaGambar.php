<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class IjasaGambar extends Model
{
    protected $table = 'ijasa_gambar'; // Nama tabel

    protected $primaryKey = 'id_ijasa_gambar'; // Primary key custom

    protected $fillable = ['tanggal', 'gambar', 'keterangan'];

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
    protected $appends = ['tanggal_formatted'];

}
