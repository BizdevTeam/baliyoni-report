<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaans';
    protected $primaryKey = 'id_perusahaan';
    protected $fillable = ['nama_perusahaan'];

    public function laporanHoldings()
    {
        return $this->hasMany(LaporanHolding::class, 'perusahaan_id', 'id_perusahaan');
    }
    public function laporanRekapPenjulanPerusahaan()
    {
        return $this->hasMany(RekapPenjualanPerusahaan::class, 'perusahaan_id', 'id_perusahaan');
    }
}

