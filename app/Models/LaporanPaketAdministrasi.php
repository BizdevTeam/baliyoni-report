<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LaporanPaketAdministrasi extends Model
{
    //
    use HasFactory;

    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'laporan_paket_administrasis';
    protected $primaryKey = 'id_laporanpaket'; // Primary key custom

    protected $fillable = [
        'tanggal', 
        'website',     
        'total_paket',        
    ];


    // Kolom yang dapat diisi menggunakan metode mass assignment
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
    
    public function getTotalPaketFormattedAttribute()
    {
        return number_format($this->total_paket, 0, ',', '.');
    }
}
