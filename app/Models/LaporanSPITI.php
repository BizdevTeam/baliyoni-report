<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanSPITI extends Model
{
     protected $table = "laporanspiti";
    protected $primaryKey = "id_spiti";
    protected $fillable = ["bulan_tahun","judul","aspek","masalah","solusi","implementasi"];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse(($this->bulan_tahun)->format("m/Y"));
    }
}
