<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArusKas extends Model
{
    use HasFactory;

    protected $table = 'arus_kass';

    protected $fillable = ['bulan_tahun', 'kas_masuk', 'kas_keluar'];
}
