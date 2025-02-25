<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekapPendapatanServisASP extends Model
{
    use HasFactory;
    protected $table = 'rekap_pendapatan_servis_asps'; // Nama tabel

    protected $primaryKey = 'id_rpsasp'; // Primary key custom

    protected $fillable = ['bulan', 'pelaksana', 'nilai_pendapatan'];

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->translatedFormat('F - Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getNilaiPendapatanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai_pendapatan, 0, ',', '.');
    }
}
