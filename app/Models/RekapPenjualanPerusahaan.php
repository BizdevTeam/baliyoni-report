<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RekapPenjualanPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'rekap_penjualan_perusahaans';
    protected $fillable = ['tanggal', 'perusahaan_id', 'total_penjualan'];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'id');
    }
    // Menambahkan accessor untuk tanggal dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getTotalPenjualanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_penjualan, 0, ',', '.');
    }
}


