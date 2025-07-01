<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxPlanning extends Model
{
    use HasFactory;

    protected $table = 'tax_plannings';

    protected $fillable = [
        'nama_perusahaan',
        'tanggal',
        'tax_planning',
        'total_penjualan',
    ];
}
