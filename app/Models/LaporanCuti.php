<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanCuti extends Model
{
    use HasFactory;
    protected $table = 'laporan_cutis'; // Nama tabel

    protected $primaryKey = 'id_cuti'; // Primary key custom

    protected $fillable = ['bulan', 'total_cuti', 'nama'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }
}
