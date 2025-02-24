<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArusKas extends Model
{
    use HasFactory;
    protected $table = 'arus_kas'; // Nama tabel

    protected $primaryKey = 'id_aruskas'; // Primary key custom

    protected $fillable = ['date', 'kas_masuk', 'kas_keluar'];

    // Menambahkan accessor untuk date dengan format 'mm/yyyy'
    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->translatedFormat('d F Y');
    }

    // Menambahkan accessor dengan format Rp
    public function getKasMasukFormattedAttribute()
    {
        return 'Rp ' . number_format($this->kas_masuk, 0, ',', '.');
    }

    public function getKasKeluarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->kas_keluar, 0, ',', '.');
    }
}
