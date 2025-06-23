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

class RentalReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    use CalculateRentalFee;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Rental $rental,
        public int $days,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Book rental is about to expire',
            from: config('email.from.address')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // $days = $this->dueDateDistance($this->rental->due_d$
        $renewUrl = env('FRONTEND_URL') . '/rentals/extend/' . $this->rental->id;

        return new Content(
            view: 'emails.rental-reminder',
            with: [
                'userName' => $this->rental->user->name,
                'rentalDetails' => $this->rental->rentalDetails,
                'rentalDate' => $this->rental->rental_date,
                'dueDate' => $this->rental->due_date,
                'daysLeft' => $this->days,
                'renewUrl' => $renewUrl,
                'contactInfo' => config('library.contact_info'),
                'email' => config('library.contact.email'),
                'phone' => config('library.contact.phone')
            ]
        );
    }
}