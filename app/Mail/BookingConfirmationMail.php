<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactions;
    public $tempPassword;
    public $paymentAccounts;
    public $hotelName;
    public $contactEmail;
    public $receiptVerifyTime;

    /**
     * Create a new message instance.
     */
    public function __construct($transactions, ?string $tempPassword, Collection $paymentAccounts)
    {
        $this->transactions      = $transactions instanceof Collection ? $transactions : collect([$transactions]);
        $this->tempPassword      = $tempPassword;
        $this->paymentAccounts   = $paymentAccounts;

        // Pull live settings
        $settings = \App\Models\Setting::query()->pluck('value', 'key');
        $this->hotelName         = $settings['hotel_name']          ?? 'Bella Vista Lodge';
        $this->contactEmail      = $settings['contact_email']       ?? config('mail.from.address');
        $this->receiptVerifyTime = $settings['receipt_verify_time'] ?? '1-2 hours';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Confirmation – Bella Vista Lodge',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_confirmation',
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
