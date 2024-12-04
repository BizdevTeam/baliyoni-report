<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KasHutangPiutangStok extends Model
{
    use HasFactory;

    protected $table = 'kas_hutang_piutang_stoks';

    protected $fillable = ['bulan_tahun', 'kas', 'hutang', 'piutang', 'stok'];
}

