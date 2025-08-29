<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPaketAdministrasi extends Model
{
    use HasFactory;

    protected $table = 'laporan_paket_administrasis';
    protected $primaryKey = 'id_laporanpaket';

    // PERUBAHAN DI SINI
    protected $fillable = [
        'tanggal', 
        'unit_bisnis_id',  // <-- 'website' diganti dengan ini
        'total_paket',      
    ];

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
    
    public function getTotalPaketFormattedAttribute()
    {
        return number_format($this->total_paket, 0, ',', '.');
    }
    
    public function unitBisnis()
    {
        return $this->belongsTo(UnitBisnis::class, 'unit_bisnis_id');
    }
}