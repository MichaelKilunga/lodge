<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Room;
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

        $this->info("Daily report for {$today->format('Y-m-d')} sent to {$ownerEmail}.");
        return 0;
    }
}
