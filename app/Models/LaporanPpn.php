<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanPpn extends Model
{
    protected $table = 'laporan_ppns';
    protected $primaryKey = 'id_laporanppn';
    protected $fillable = ['bulan','thumbnail','file','keterangan'];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }

}
