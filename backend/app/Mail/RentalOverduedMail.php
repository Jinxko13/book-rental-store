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

class RentalOverduedMail extends Mailable
{
    use Queueable, SerializesModels;
    use CalculateRentalFee;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Rental $rental
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            subject: "Notifier: Book rental has expired",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $daysOverdue = $this->dueDateDistance($this->rental->due_date);
        $lateFee = $this->calculateLateFee($this->rental->due_date);
        $renewUrl = env('FRONTEND_URL') . '/rentals/extend/' . $this->rental->id;
        
        return new Content(
            view: 'emails.rental-overdued',
            with: [
                'userName' => $this->rental->user->name,
                'rentalDetails' => $this->rental->rentalDetails,
                'dueDate' => $this->rental->due_date,
                'daysOverdue' => abs($daysOverdue),
                'lateFee' => $lateFee,
                'renewUrl' => $renewUrl,
                'contactInfo' => config('library.contact_info'),
                'email' => config('library.contact.email'),
                'phone' => config('library.contact.phone')
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
