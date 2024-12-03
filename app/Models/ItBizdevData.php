<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItBizdevData extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'it_bizdev_datas';

    // Primary Key (Custom)
    protected $primaryKey = 'id_bizdevdata';

    // Kolom yang dapat diisi
    protected $fillable = [
        'bizdevbulanan_id', // Foreign Key
        'aplikasi',
        'kondisi_bulanlalu',
        'kondisi_bulanini',
        'update',
        'rencana_implementasi',
        'keterangan'
    ];

    // Relasi dengan ItBizdevBulanan
    public function bulanan()
    {
        return $this->belongsTo(ItBizdevBulanan::class, 'bizdevbulanan_id');
    }
}
