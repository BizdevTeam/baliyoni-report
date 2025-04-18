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
    protected $fillable = [
    'tanggal', 
    'pelaksana',
    'total_pengiriman',
];

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor untuk kas, hutang, piutang, stok dengan format Rp
    public function getTotalPengirimanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_pengiriman, 0, ',', '.');
    }
    
}
