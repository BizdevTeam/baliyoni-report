<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanPpn extends Model
{
    protected $table = 'laporan_ppns';
    protected $primaryKey = 'id_laporanppn';
    protected $fillable = ['date','thumbnail','file','keterangan'];

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }

}
