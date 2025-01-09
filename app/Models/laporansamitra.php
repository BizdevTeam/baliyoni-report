<?php

namespace App\Models;
use carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanSamitra extends Model
{
    use HasFactory;

    protected $table = 'laporan_samitras';
    protected $primaryKey = 'id_samitra'; // Primary key custom
    protected $fillable = ['bulan', 'total_pengiriman'];

    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }

    // Menambahkan accessor untuk kas, hutang, piutang, stok dengan format Rp
    public function getTotalPengirimanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_pengiriman, 0, ',', '.');
    }
    
}
