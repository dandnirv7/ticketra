<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Booking $booking, public string $qrCodeBase64) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Ticket Booking ' . $this->booking->booking_id,
            tags: ['booking', 'e-ticket'],
            metadata: [
                'booking_id' => $this->booking->booking_id,
                'user_id' => $this->booking->user_id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'qrCodeBase64' => $this->qrCodeBase64,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.booking-pdf', [
            'booking' => $this->booking,
            'qrCodeBase64' => $this->qrCodeBase64,
        ])
            ->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn() => $pdf->output())
                ->as('e-ticket-' . $this->booking->booking_id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
