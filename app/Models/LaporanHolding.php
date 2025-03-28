<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHolding extends Model
{
    use HasFactory;

    protected $table = 'laporan_holdings'; // Sesuaikan dengan nama tabel

    protected $fillable = [
        'tanggal',
        'perusahaan_id',
        'nilai',
    ];
    

    // Relasi ke model Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'id');
    }

    public function getNilaiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai, 0, ',', '.');
    }

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
