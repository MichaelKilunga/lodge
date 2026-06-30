<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentAccountController extends Controller
{
    public function index()
    {
        $accounts = \App\Models\PaymentAccount::all();
        return view('payment_account.index', compact('accounts'));
    }

    public function create()
    {
        return view('payment_account.create');
    }

    public function show(\App\Models\PaymentAccount $paymentAccount)
    {
        return redirect()->route('payment-account.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        \App\Models\PaymentAccount::create($request->all());
        return redirect()->route('payment-account.index')->with('success', 'Payment Account created successfully.');
    }

    public function edit(\App\Models\PaymentAccount $paymentAccount)
    {
        return view('payment_account.edit', compact('paymentAccount'));
    }

    public function update(Request $request, \App\Models\PaymentAccount $paymentAccount)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        $paymentAccount->update($request->all());
        return redirect()->route('payment-account.index')->with('success', 'Payment Account updated successfully.');
    }

    public function destroy(\App\Models\PaymentAccount $paymentAccount)
    {
        $paymentAccount->delete();
        return redirect()->route('payment-account.index')->with('success', 'Payment Account deleted successfully.');
    }
}
