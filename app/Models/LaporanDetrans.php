<?php

namespace App\Models;
use carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanDetrans extends Model
{
    use HasFactory;

    protected $table = 'laporan_detrans';
    protected $primaryKey = 'id_detrans'; // Primary key custom
    protected $fillable = ['bulan', 'total_pengiriman'];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }

    // Menambahkan accessor untuk kas, hutang, piutang, stok dengan format Rp
    public function getTotalPengirimanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_pengiriman, 0, ',', '.');
    }
    
}
