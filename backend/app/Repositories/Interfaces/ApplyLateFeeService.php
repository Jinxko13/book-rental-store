<?php

namespace App\Repositories\Interfaces;

use App\Models\Rental;
use App\Services\ReturnBook\LateFeeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ApplyLateFeeService
{
    protected $lateFeeService;

    public function __construct(LateFeeService $lateFeeService)
    {
        $this->lateFeeService = $lateFeeService;
    }

    public function handle(Rental $rental)
    {
        $rentalEndDate = Carbon::parse($rental->due_date);
        $returnDate = Carbon::now();

        $lateFee = $this->lateFeeService->calculateLateFee($rentalEndDate, $returnDate);
        Log::info("✔ Rental ID: {$rental->id} - Late Fee: {$lateFee}");

        $daysRented = Carbon::parse($rental->rental_date)->diffInDays($rentalEndDate);
        $rentalFee = $daysRented * 5000;

        $refundAmount = max($rental->deposit - $lateFee - $rentalFee, 0);

        $rental->late_fee = $lateFee;
        $rental->refund_amount = $refundAmount;
        $rental->save();

        Log::info("✔ Rental ID: {$rental->id} - Refund Amount: {$refundAmount}");
    }
}
