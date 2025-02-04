<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanIjasa extends Model
{
    use HasFactory;
    protected $table = 'laporan_ijasas'; // Nama tabel

    protected $primaryKey = 'id_ijasa'; // Primary key custom

    protected $fillable = ['tanggal', 'jam', 'permasalahan', 'impact', 'troubleshooting', 'resolve_tanggal', 'resolve_jam','gambar'];

    // Menambahkan accessor untuk tanggal dengan format 'd/m/y'
    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->format('d/m/y') : '-';
    }

    public function getResolveFormattedAttribute()
    {
        return $this->resolve_tanggal ? Carbon::parse($this->resolve_tanggal)->format('d/m/y') : '-';
    }

    public function setJamAttribute($value)
    {
        $this->attributes['jam'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
    }
    public function setResolveJamAttribute($value)
    {
        $this->attributes['resolve_jam'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
    }


}
