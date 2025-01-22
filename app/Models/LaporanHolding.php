<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanHolding extends Model
{
    use HasFactory;
    protected $table = 'laporan_holdings'; // Nama tabel

    protected $primaryKey = 'id_holding'; // Primary key custom

    protected $fillable = ['bulan', 'perusahaan', 'nilai'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
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
