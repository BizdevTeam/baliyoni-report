<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasis';

    protected $fillable = [ 
        'divisi',     
        'target_realisasi',        
        'analisa_penyimpangan',        
        'alternative_solusi',        
    ];


    // Kolom yang dapat diisi menggunakan metode mass assignment
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
    
}
