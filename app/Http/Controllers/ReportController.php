<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        switch ($period) {
            case 'week':
                $from = Carbon::now()->startOfWeek();
                $to   = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $from = Carbon::now()->startOfMonth();
                $to   = Carbon::now()->endOfMonth();
                break;
            default: // today
                $from = Carbon::now()->startOfDay();
                $to   = Carbon::now()->endOfDay();
                break;
        }

        $transactions = Transaction::with(['room.type', 'customer'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $totalRevenue = $transactions->sum(function ($t) {
            return optional($t->payment)->total ?? 0;
        });

        $totalBookings = $transactions->count();

        // Occupancy: rooms booked vs total rooms
        $totalRooms = \App\Models\Room::count();
        $occupiedRooms = $transactions->pluck('room_id')->unique()->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        return view('report.index', compact(
            'transactions', 'totalRevenue', 'totalBookings', 'occupancyRate', 'period', 'from', 'to'
        ));
    }
}
