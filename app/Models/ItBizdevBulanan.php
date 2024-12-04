<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItBizdevBulanan extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'it_bizdev_bulanans';

    // Primary Key (Custom)
    protected $primaryKey = 'id_bizdevbulanan';

    // Kolom yang dapat diisi
    protected $fillable = ['bulan', 'judul'];

    // Relasi dengan ItBizdevData
    public function datas()
    {
        return $this->hasMany(ItBizdevData::class, 'bizdevbulanan_id');
    }

    // Menambahkan accessor untuk bulan dengan format 'mm/yyyy'
    public function getBulanFormattedAttribute()
    {
        return Carbon::parse($this->bulan)->format('m/Y');
    }
}
