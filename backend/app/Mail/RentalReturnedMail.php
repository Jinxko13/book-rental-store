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

class RentalReturnedMail extends Mailable
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
            subject: 'Rental returned successfully',
            from: config('email.from.address')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $rentalDuration = Carbon::parse($this->rental->rental_date)->diffInDays(Carbon::parse($this->rental->returns->created_at));
        $browseUrl = env('FRONTEND_URL');
        return new Content(
            view: 'emails.rental-returned',
            with: [
                'userName' => $this->rental->user->name,
                'rentalDetails' => $this->rental->rentalDetails,
                'returnDate' => $this->rental->returns->created_at ? $this->rental->returns->created_at->format('d/m/Y') : 'N/A',
                'rentalDuration' => $rentalDuration,
                'browseUrl' => $browseUrl,
                'contactInfo' => config('library.contact_info'),
                'email' => config('library.contact.email'),
                'phone' => config('library.contact.phone')
            ]
        );
    }
}