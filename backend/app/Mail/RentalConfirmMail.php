<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Rental;
use App\Traits\CalculateRentalFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalConfirmMail extends Mailable
{
    use Queueable, SerializesModels;
    use CalculateRentalFee;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Rental $rental,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Book rental infomation',
            from: config('email.from.address')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $daysLeft = $this->dueDateDistance($this->rental->due_date);
        $baseFee = config('library.rental_fee_per_day');
        $lateFeeRate = config('library.late_fee_rate');
        $lateFee = $baseFee * $lateFeeRate;

        $url = env('FRONTEND_URL') . '/rentals/extend/' . $this->rental->id;
        if (is_string($this->rental->rental_date)) {
            $rental_date = $this->rental->rental_date;
        } else {
            $rental_date = $this->rental->rental_date->format('Y-m-d H:i:s');
        }

        if (is_string($this->rental->due_date)) {
            $due_date = $this->rental->due_date;
        } else {
            $due_date = $this->rental->due_date->format('Y-m-d H:i:s');
        }

        return new Content(
            view: 'emails.rental-confirm',
            with: [
                'userName' => $this->rental->user->name,
                'rentalDetails' => $this->rental->rentalDetails,
                'rentalDate' => $rental_date,
                'dueDate' => $due_date,
                'lateFee' => $lateFee,
                'url' => $url,
                'contactInfo' => config('library.contact_info'),
                'email' => config('library.contact.email'),
                'phone' => config('library.contact.phone')
            ]
        );
    }
}
