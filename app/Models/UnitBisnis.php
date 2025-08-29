<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitBisnis extends Model
{
    protected $table = 'unit_bisnis';
    protected $fillable = ['nama_unit'];

// In app/Models/UnitBisnis.php

public function laporanPaketAdministrasi()
{
    // Hapus argumen ketiga ('id_unit')
    return $this->hasMany(LaporanPaketAdministrasi::class, 'unit_bisnis_id');
}}

