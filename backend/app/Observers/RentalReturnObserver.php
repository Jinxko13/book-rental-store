<?php

namespace App\Observers;

use App\Models\RentalReturn;
use App\Mail\RentalReturnedMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RentalReturnObserver
{
    /**
     * Handle the Rental "created" event.
     */
    public function created(RentalReturn $rentalReturn): void
    {
        $rental = $rentalReturn->rental;
        Mail::to($rental->user->email)->send(new RentalReturnedMail($rental));
    }
}
