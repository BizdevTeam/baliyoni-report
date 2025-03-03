<?php

namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait DateValidationTraitAccSPI
{
    public function isInputAllowed($date, &$errorMessage, $extendToNextMonth = false)
    {
        $currentDate = now();

        //mengambil role pengguna yang login
        $userRole = Auth::check() ? Auth::user()->role : null;
        //role superadmin tidak perlu validasi tanggal
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

        // Batas awal selalu tanggal 1 bulan berjalan
        $startDate = $currentDate->copy()->startOfMonth();

        // Batas akhir default: tanggal 27 bulan berjalan
        $endDate = $currentDate->copy()->startOfMonth()->addDays(27); // 27 current month

        // Jika extend aktif, batas akhir diperpanjang ke tanggal 3 bulan depan
        if ($extendToNextMonth) {
            $endDate = $currentDate->copy()->addMonth()->startOfMonth()->addDays(); // 3 next month
        }

        // Periksa apakah tanggal berada dalam rentang yang diizinkan
        if ($inputDate->lt($startDate) || $inputDate->gt($endDate)) {
            $errorMessage = 'Input hanya diizinkan dari tanggal 1 hingga ' . $endDate->format('d F Y') . '.';
            return false;
        }

        return true;
    }
}