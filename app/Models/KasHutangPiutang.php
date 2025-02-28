<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KasHutangPiutang extends Model
{
    //
    use HasFactory;
    protected $table = 'kas_hutang_piutangs'; // Nama tabel

    protected $primaryKey = 'id_khps'; // Primary key custom

    protected $fillable = ['tanggal', 'kas', 'hutang', 'piutang', 'stok'];

    // Menambahkan accessor untuk tanggal dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getKasFormattedAttribute()
    {
        return 'Rp ' . number_format($this->kas, 0, ',', '.');
    }

    public function getHutangFormattedAttribute()
    {
        return 'Rp ' . number_format($this->hutang, 0, ',', '.');
    }

    public function getPiutangFormattedAttribute()
    {
        return 'Rp ' . number_format($this->piutang, 0, ',', '.');
    }

    public function getStokFormattedAttribute()
    {
        return 'Rp ' . number_format($this->stok, 0, ',', '.');
    }
}
