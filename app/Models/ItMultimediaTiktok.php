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

    protected $fillable = ['date', 'gambar', 'keterangan'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
    protected $appends = ['date_formatted'];



}
