<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanNeraca extends Model
{
    use HasFactory;
    protected $table = 'laporan_neracas'; // Nama tabel

    protected $primaryKey = 'id_neraca'; // Primary key custom

    protected $fillable = ['tanggal', 'gambar', 'file_excel', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

}
