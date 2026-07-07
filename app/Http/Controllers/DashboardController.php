<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isCustomer()) {
            $customer = auth()->user()->customer;
            $transactions = collect();
            if ($customer) {
                $transactions = Transaction::with('room.type', 'room.image', 'payment')
                    ->where('customer_id', $customer->id)
                    ->orderBy('id', 'desc')
                    ->get();
            }
            $paymentAccounts = \App\Models\PaymentAccount::all();

            // Derived stats for the advanced dashboard
            $totalSpent        = $transactions->sum(fn ($t) => $t->getTotalPayment());
            $activeBookings    = $transactions->whereIn('status', ['Reservation', 'Active'])->count();
            $completedBookings = $transactions->where('status', 'Done')->count();
            $canceledBookings  = $transactions->where('status', 'Canceled')->count();
            $pendingPayment    = $transactions->where('status', 'Reservation')
                                             ->sum(fn ($t) => max(0, $t->getTotalPrice() - $t->getTotalPayment()));
            $upcomingBooking   = $transactions->where('status', 'Reservation')
                                             ->sortBy('check_in')->first();

            return view('dashboard.customer', compact(
                'transactions', 'paymentAccounts', 'customer',
                'totalSpent', 'activeBookings', 'completedBookings',
                'canceledBookings', 'pendingPayment', 'upcomingBooking'
            ));
        }

        $transactions = Transaction::with('user', 'room', 'customer')
            ->where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()]])
            ->orderBy('check_out', 'ASC')
            ->orderBy('id', 'DESC')
            ->get();

        return view('dashboard.index', [
            'transactions' => $transactions,
        ]);
    }
}
