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

        $transactions = Transaction::with(['room.type', 'customer', 'payment'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $transactionRevenue = $transactions->sum(function ($t) {
            return $t->getTotalPayment();
        });
        $periodPayments = \App\Models\Payment::whereBetween('created_at', [$from, $to])->sum('price');
        $totalRevenue = max($transactionRevenue, $periodPayments);

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
