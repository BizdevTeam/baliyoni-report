<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StatusPaket extends Model
{
    //
    use HasFactory;

    // Tabel yang akan digunakan (opsional jika nama tabel sesuai konvensi Laravel, yaitu "laporan_paket_administrasis")
    protected $table = 'status_pakets';
    protected $primaryKey = 'id_statuspaket'; // Primary key custom
    // Kolom yang dapat diisi menggunakan metode mass assignment
    protected $fillable = [
        'tanggal',      // Format bulan dan tahun (contoh: '11/2024')
        'status',          // Nama website (string)
        'total_paket',         // Nilai paket dalam rupiah (integer)
    ];

    // Kolom yang dapat diisi menggunakan metode mass assignment
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
    
}
