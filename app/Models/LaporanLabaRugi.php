<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanLabaRugi extends Model
{
    use HasFactory;
    protected $table = 'laporan_laba_rugis'; // Nama tabel

    protected $primaryKey = 'id_labarugi'; // Primary key custom

    protected $fillable = ['date', 'gambar', 'file_excel', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
