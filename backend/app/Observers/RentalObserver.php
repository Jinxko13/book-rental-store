<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Rental;
use App\Mail\RentalOverduedMail;
use App\Mail\RentalConfirmMail;
use App\Mail\RentalReturnedMail;
use App\Mail\RentalReminderMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RentalObserver
{
    /**
     * Handle the Rental "created" event.
     */
    public function created(Rental $rental): void
    {
        Mail::to($rental->user->email)->send(new RentalConfirmMail($rental));
    }

    /**
     * Handle the Rental "updated" event.
     */
    public function updated(Rental $rental): void
    {
        if ($rental->wasChanged('due_date')) {
            $rentalEndDate = Carbon::parse($rental->due_date);
            $currentDate = Carbon::now();
            if ($currentDate->gt($rentalEndDate)) {
                if ($rental->status != 'overdue') {
                    $rental->status = 'overdue';
                    $rental->save();
                }
            } elseif ($currentDate->lte($rentalEndDate)) {
                if ($rental->status != 'renting') {
                    $rental->status = 'renting';
                    $rental->save();
                }
            }
        }
    }

    /**
     * Handle the Rental "deleted" event.
     */
    public function deleted(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "restored" event.
     */
    public function restored(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "force deleted" event.
     */
    public function forceDeleted(Rental $rental): void
    {
        //
    }
}
