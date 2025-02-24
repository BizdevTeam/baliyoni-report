<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanSPITI extends Model
{
     protected $table = "laporanspiti";
    protected $primaryKey = "id_spiti";
    protected $fillable = ["date","aspek","masalah","solusi","implementasi"];

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
