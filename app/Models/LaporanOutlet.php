<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanOutlet extends Model
{
    //
    use HasFactory;
    protected $table = 'laporan_outlets'; // Nama tabel

    protected $primaryKey = 'id_outlet'; // Primary key custom

    protected $fillable = ['bulan', 'total_pembelian'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getTotalPembelianFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_pembelian, 0, ',', '.');
    }
}
