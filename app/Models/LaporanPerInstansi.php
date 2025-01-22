<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPerInstansi extends Model
{
    use HasFactory;
    protected $table = 'laporan_per_instansis';
    protected $primaryKey = 'id_perinstansi';

    protected $fillable = [
        'bulan',      
        'instansi',          
        'nilai',         
    ];

    // Kolom yang dapat diisi menggunakan metode mass assignment
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getNilaiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }


}