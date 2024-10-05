<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;
    public $event;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($booking, $event)
    {
        $this->booking = $booking;
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Booking Confirmation')
            ->view('emails.booking-confirmation')
            ->with([
                'booking' => $this->booking,
                'event' => $this->event,
            ]);
    }
}
