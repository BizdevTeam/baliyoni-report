<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItMultimediaTiktok extends Model
{
    //
    use HasFactory;
    protected $table = 'it_multimedia_tiktoks'; // Nama tabel

    protected $primaryKey = 'id_tiktok'; // Primary key custom

    protected $fillable = ['bulan', 'gambar', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }


}
