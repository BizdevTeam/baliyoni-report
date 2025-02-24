<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanSPI extends Model
{
    protected $table = "laporan_spis";
    protected $primaryKey = "id_spi";
    protected $fillable = ["date","aspek","masalah","solusi","implementasi"];
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
