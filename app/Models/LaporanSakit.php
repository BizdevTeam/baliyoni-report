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

    protected $fillable = ['date', 'total_sakit', 'nama'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }
}
