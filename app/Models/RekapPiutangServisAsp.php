<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekapPiutangServisASP extends Model
{
    use HasFactory;
    protected $table = 'rekap_piutang_servis_a_s_p_s'; // Nama tabel

    protected $primaryKey = 'id_rpiutangsasp'; // Primary key custom

    protected $fillable = ['date', 'pelaksana', 'nilai_piutang']; // Kolom yang dapat diisi

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getNilaiPiutangFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai_piutang, 0, ',', '.');
    }
}
