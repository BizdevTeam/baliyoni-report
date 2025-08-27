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
        if ($inputDate->lt($currentDate->copy()->startOfMonth())) {
            $errorMessage = 'Input tidak diizinkan untuk bulan sebelumnya.';
            return false;
        }

        // Input should not be from a future date after the end of the current month
        if ($inputDate->gt($currentDate->copy()->endOfMonth())) {
            $errorMessage = 'Input hanya diizinkan untuk bulan berjalan.';
            return false;
        }

        // The check for day > 27 has been removed.
        // The logic above already handles the end of the month correctly (e.g., 28, 29, 30, or 31).

        return true;
    }
}
