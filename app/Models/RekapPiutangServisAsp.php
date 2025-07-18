<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekapPiutangServisAsp extends Model
{
    use HasFactory;
    protected $table = 'rekap_piutang_servis_a_s_p_s'; // Nama tabel

    protected $primaryKey = 'id_rpiutangsasp'; // Primary key custom

    protected $fillable = ['tanggal', 'pelaksana', 'nilai_piutang']; // Kolom yang dapat diisi

    // Menambahkan accessor untuk tanggal dengan format 'mm/yyyy'
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getNilaiPiutangFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai_piutang, 0, ',', '.');
    }
}
