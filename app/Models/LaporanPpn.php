<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaporanPpn extends Model
{
    protected $table = 'laporan_ppns';
    protected $primaryKey = 'id_laporanppn';
    protected $fillable = ['tanggal','thumbnail','file','keterangan'];

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

}
