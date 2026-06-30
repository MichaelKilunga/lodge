<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'Customer') {
            $customer = auth()->user()->customer;
            $transactions = [];
            if ($customer) {
                $transactions = Transaction::with('room', 'payment')
                    ->where('customer_id', $customer->id)
                    ->orderBy('id', 'desc')
                    ->get();
            }
            $paymentAccounts = \App\Models\PaymentAccount::all();

            return view('dashboard.customer', compact('transactions', 'paymentAccounts'));
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
