<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanSPI extends Model
{
    protected $table = "laporan_spis";
    protected $primaryKey = "id_spi";
    protected $fillable = ["tanggal","aspek","masalah","solusi","implementasi"];
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
