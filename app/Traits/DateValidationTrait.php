<?php

namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait DateValidationTrait
{
    public function isInputAllowed($tanggal, &$errorMessage)
    {
        $currentDate = now();

        // Get the logged-in user's role
        $userRole = Auth::check() ? Auth::user()->role : null;

        // Superadmin bypasses all date validations
        if ($userRole === 'superadmin') {
            return true;
        }

        try {
            // Convert input date to Carbon (ensure correct format)
            $inputDate = Carbon::createFromFormat('Y-m-d', $tanggal);
        } catch (\Exception $e) {
            $errorMessage = 'Format tanggal tidak valid.';
            return false;
        }

        // Input should not be from a previous month
        if ($inputDate->lt($currentDate->startOfMonth())) {
            $errorMessage = 'Input tidak diizinkan untuk bulan sebelumnya.';
            return false;
        }

        // Input should not be from a future month
        if ($inputDate->gt($currentDate->endOfMonth())) {
            $errorMessage = 'Input hanya diizinkan untuk bulan berjalan.';
            return false;
        }

        // Input should not be after the 27th of the current month
        if ($inputDate->day > 27) {
            $errorMessage = 'Input tidak diizinkan setelah tanggal 27 pukul 23:59.';
            return false;
        }

        return true;
    }
}
