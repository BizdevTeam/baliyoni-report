<?php

namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait DateValidationTrait
{
    public function isInputAllowed($date, &$errorMessage)
    {
        $currentDate = now();

        // Ambil role pengguna yang login
        $userRole = Auth::check() ? Auth::user()->role : null;

        // Jika user adalah superadmin, maka tidak perlu validasi tanggal
        if ($userRole === 'superadmin') {
            return true;
        }

        try {
            // Konversi input date ke Carbon (pastikan format yang benar)
            $inputDate = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            $errorMessage = 'Format tanggal tidak valid.';
            return false;
        }

        // Tidak boleh input untuk bulan sebelumnya
        if ($inputDate->lt($currentDate->startOfMonth())) {
            $errorMessage = 'Input tidak diizinkan untuk bulan sebelumnya.';
            return false;
        }

        // Tidak boleh input untuk bulan berikutnya
        if (!$inputDate->isSameMonth($currentDate)) {
            $errorMessage = 'Input hanya diizinkan untuk bulan berjalan.';
            return false;
        }

        // Tidak boleh input setelah tanggal 27 di bulan berjalan
        if ($inputDate->isSameMonth($currentDate) && $currentDate->day > 27) {
            $errorMessage = 'Input tidak diizinkan setelah tanggal 27 pukul 23:59.';
            return false;
        }

        return true;
    }
}
