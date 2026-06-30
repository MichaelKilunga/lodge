<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $totalBookings;
    public $totalRevenue;
    public $occupancyRate;
    public $reportDate;

    public function __construct($totalBookings, $totalRevenue, $occupancyRate, $reportDate)
    {
        $this->totalBookings  = $totalBookings;
        $this->totalRevenue   = $totalRevenue;
        $this->occupancyRate  = $occupancyRate;
        $this->reportDate     = $reportDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Report - ' . $this->reportDate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
