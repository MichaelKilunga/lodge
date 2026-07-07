<?php

namespace App\Mail;

use App\Models\MarketingCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketingCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $hotelName;
    public $contactEmail;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketingCampaign $campaign, string $recipientName = 'Valued Guest')
    {
        $this->campaign = $campaign;
        $this->recipientName = $recipientName;

        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $this->hotelName = $settings['hotel_name'] ?? 'Bella Vista Lodge';
        $this->contactEmail = $settings['contact_email'] ?? config('mail.from.address', 'info@bellavistalodge.com');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.marketing_campaign',
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
