<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanSPITI extends Model
{
     protected $table = "laporanspiti";
    protected $primaryKey = "id_spiti";
    protected $fillable = ["tanggal","aspek","masalah","solusi","implementasi"];

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}
