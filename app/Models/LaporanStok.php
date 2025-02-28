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

    protected $fillable = ['tanggal', 'stok'];

    // Menambahkan accessor untuk tanggal dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getStokFormattedAttribute()
    {
        return 'Rp ' . number_format($this->stok, 0, ',', '.');
    }
}
