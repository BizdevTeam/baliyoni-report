<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanStok extends Model
{
    //
    use HasFactory;
    protected $table = 'laporan_stoks'; // Nama tabel

    protected $primaryKey = 'id_stok'; // Primary key custom

    protected $fillable = ['bulan', 'stok'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getStokFormattedAttribute()
    {
        return 'Rp ' . number_format($this->stok, 0, ',', '.');
    }
}
