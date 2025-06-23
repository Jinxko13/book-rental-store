<?php

namespace App\Traits;

use Carbon\Carbon;

trait CalculateRentalFee
{
    public function dueDateDistance($dueDate)
    {
        return round(Carbon::parse($dueDate)->diffInDays(Carbon::now()));
    }

    public function calculateLateFee($dueDate, $returnDate)
    {
        $baseFee = config('library.rental_fee_per_day');
        $lateFeeRate = config('library.late_fee_rate');
        $dueDate = Carbon::parse($dueDate);
        $returnDate = Carbon::parse($returnDate);
        $daysLate = abs($dueDate->diffInDays($returnDate));

        return $daysLate * $baseFee * $lateFeeRate;
    }

    public function calculateRentalFee($rentalDate, $dueDate){
        $baseFee = config('library.rental_fee_per_day');
        
        $rentalDate = Carbon::parse($rentalDate);
        $dueDate = Carbon::parse($dueDate);
        $daysRented = $rentalDate->diffInDays($dueDate);

        if ($daysRented <= 7) {
            $rentalFeePerDay = $baseFee;
        } elseif ($daysRented <= 14) {
            $rentalFeePerDay = $baseFee * 1.25;
        } elseif ($daysRented <= 21) {
            $rentalFeePerDay = $baseFee * 1.5;
        } else {
            $rentalFeePerDay = $baseFee * 1.75;
        }

        $totalRentalFee = $daysRented * $rentalFeePerDay;

        return $totalRentalFee;
    }
}
