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

        // 1. Get transactions created today
        $transactionsCreatedToday = Transaction::whereDate('created_at', $today)->get();
        $totalBookings  = $transactionsCreatedToday->count();

        // 2. Sum up payments received today (revenue)
        $totalRevenue   = \App\Models\Payment::whereDate('created_at', $today)->sum('price');

        // 3. Count occupied rooms today (active stay dates overlapping today)
        $totalRooms     = Room::count();
        $occupiedRooms  = Transaction::where('check_in', '<=', $today)
            ->where('check_out', '>=', $today)
            ->where('status', '!=', 'Canceled')
            ->pluck('room_id')
            ->unique()
            ->count();
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
            $smsText   = "Daily Report for {$hotelName} - {$today->format('d M Y')}:\n"
                       . "Bookings: {$totalBookings} | Occupancy: {$occupancyRate}% | Revenue: " . number_format((float)$totalRevenue, 0, '.', ',');
            SmsService::send($adminPhone, $smsText);
        }


        $this->info("Daily report for {$today->format('Y-m-d')} sent to {$ownerEmail}.");
        return 0;
    }
}
