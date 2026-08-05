<?php

namespace Tests\Feature;

use App\Mail\DailyReportMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendDailyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_report_sent_to_multiple_owner_emails(): void
    {
        Mail::fake();

        Setting::updateOrCreate(
            ['key' => 'owner_email'],
            ['value' => 'owner1@example.com, owner2@example.com, manager@example.com']
        );

        $this->artisan('app:send-daily-report')
            ->assertExitCode(0);

        Mail::assertSent(DailyReportMail::class, function ($mail) {
            return $mail->hasTo('owner1@example.com')
                && $mail->hasTo('owner2@example.com')
                && $mail->hasTo('manager@example.com');
        });
    }

    public function test_daily_report_fails_when_no_owner_email_set(): void
    {
        Setting::where('key', 'owner_email')->delete();

        $this->artisan('app:send-daily-report')
            ->assertExitCode(1);
    }
}
