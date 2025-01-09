<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanSakit extends Model
{
    use HasFactory;
    protected $table = 'laporan_sakits'; // Nama tabel

    protected $primaryKey = 'id_sakit'; // Primary key custom

    protected $fillable = ['bulan', 'total_sakit', 'nama'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }
}
