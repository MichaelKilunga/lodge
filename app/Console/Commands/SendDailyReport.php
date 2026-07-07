<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Room;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-daily-report';

    /**
     * The console command description.
     */
    protected $description = 'Send the daily performance report to the configured owner email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ownerEmail = Setting::where('key', 'owner_email')->value('value');

        if (empty($ownerEmail)) {
            $this->error('Owner email is not configured in Settings. Aborting.');
            return 1;
        }

        $today = Carbon::today();

        $transactions = Transaction::with('payment')
            ->whereDate('created_at', $today)
            ->get();

        $totalBookings  = $transactions->count();
        $totalRevenue   = $transactions->sum(fn($t) => optional($t->payment)->total ?? 0);
        $totalRooms     = Room::count();
        $occupiedRooms  = $transactions->pluck('room_id')->unique()->count();
        $occupancyRate  = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        Mail::to($ownerEmail)->send(new DailyReportMail(
            $totalBookings,
            $totalRevenue,
            $occupancyRate,
            $today->format('l, F d, Y')
        ));

        // Send daily summary SMS in parallel to the configured admin recipient
        $adminPhone = Setting::where('key', 'admin_sms_recipient')->value('value');
        if ($adminPhone) {
            $hotelName = Setting::where('key', 'hotel_name')->value('value') ?? config('app.name');
            $smsText   = "[{$hotelName}] Daily Report – {$today->format('d M Y')}:\n"
                       . "Bookings: {$totalBookings} | Occupancy: {$occupancyRate}% | Revenue: " . number_format((float)$totalRevenue, 0, '.', ',');
            SmsService::send($adminPhone, $smsText);
        }

        $this->info("Daily report for {$today->format('Y-m-d')} sent to {$ownerEmail}.");
        return 0;
    }
}
