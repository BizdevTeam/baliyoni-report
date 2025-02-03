<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItMultimediaInstagram extends Model
{
    //
    use HasFactory;
    protected $table = 'it_multimedia_instagrams'; // Nama tabel

    protected $primaryKey = 'id_instagram'; // Primary key custom

    protected $fillable = ['bulan', 'gambar', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }


}
