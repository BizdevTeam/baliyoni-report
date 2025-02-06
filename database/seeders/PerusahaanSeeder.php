<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perusahaan = [
            'PT. BALI UNGGUL SEJAHTERA',
            'CV. DANA RASA',
            'CV. LAGAAN SAKETI',
            'CV. BALI JAKTI INFORMATIK',
            'CV. BALI LINGGA KOMPUTER',
            'CV. ARTSOLUTION',
            'PT. BALI LINGGA SAKA GUMI',
            'CV. SAHABAT UTAMA',
            'CV. N & b NET ACCESS',
            'PT. ELKA SOLUTION NUSANTARA',
            'CV. ARINDAH',
            'ARFALINDO'
        ];

        foreach ($perusahaan as $perusahaans) {
            Perusahaan::create(['name' => $perusahaans]);
        }
    }
}
