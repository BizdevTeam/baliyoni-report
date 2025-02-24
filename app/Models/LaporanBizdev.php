<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LaporanBizdev extends Model
{
    protected $table = "laporan_bizdevs";

    protected $primaryKey = "id_bizdev";

    protected $fillable = [
        'date',
        'aplikasi',
        'kondisi_bulanlalu',
        'kondisi_bulanini',
        'update',
        'rencana_implementasi',
        'keterangan'
    ];

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
