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
        'tanggal',      
        'instansi',          
        'nilai',         
    ];

    // Kolom yang dapat diisi menggunakan metode mass assignment
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getNilaiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }


}