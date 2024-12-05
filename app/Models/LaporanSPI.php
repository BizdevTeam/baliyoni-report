<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanSPI extends Model
{
    protected $table = "laporan_spis";
    protected $primaryKey = "id_spi";
    protected $fillable = ["bulan_tahun","judul","aspek","masalah","solusi","implementasi"];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse(($this->bulan_tahun)->format("m/Y"));
    }
}
