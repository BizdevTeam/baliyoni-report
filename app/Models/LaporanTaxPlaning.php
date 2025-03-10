<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanTaxPlaning extends Model
{
    use HasFactory;
    protected $table = 'laporan_tax_planings'; // Nama tabel

    protected $primaryKey = 'id_taxplaning'; // Primary key custom

    protected $fillable = ['tanggal', 'gambar', 'file_excel', 'keterangan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

}
