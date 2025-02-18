<?php

namespace App\Traits;
use Carbon\Carbon;

trait DateValidationTrait
{
    public function isInputAllowed($bulan, &$errorMessage)
    {
        $currentDate = now();
        $inputDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();

        // Tidak boleh input untuk bulan sebelumnya
        if ($inputDate->lt($currentDate->startOfMonth())) {
            $errorMessage = 'Input tidak diizinkan untuk bulan sebelumnya.';
            return false;
        }

        // Tidak boleh input untuk bulan depan
        if ($inputDate->gt($currentDate->startOfMonth())) {
            $errorMessage = 'Input tidak diizinkan untuk bulan berikutnya.';
            return false;
        }

        // Tidak boleh input setelah tanggal 27 di bulan berjalan
        if ($currentDate->month == $inputDate->month && $currentDate->day > 27) {
            $errorMessage = 'Input tidak diizinkan setelah tanggal 27 pukul 23:59.';
            return false;
        }

        return true;
    }
}
