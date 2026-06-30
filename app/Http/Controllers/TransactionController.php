<?php

namespace App\Http\Controllers;

use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransaction($request);
        $transactionsExpired = $this->transactionRepository->getTransactionExpired($request);

        return view('transaction.index', [
            'transactions' => $transactions,
            'transactionsExpired' => $transactionsExpired,
        ]);
    }

    public function cancel(\App\Models\Transaction $transaction)
    {
        if ($transaction->status == 'Reservation') {
            $transaction->status = 'Canceled';
            $transaction->save();
            return redirect()->back()->with('success', 'Booking canceled successfully.');
        }
        return redirect()->back()->with('error', 'Cannot cancel this booking.');
    }

    public function uploadReceipt(Request $request, \App\Models\Transaction $transaction)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            'price' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string'
        ]);

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/receipts'), $filename);

            \App\Models\Payment::create([
                'user_id' => auth()->user()->id,
                'transaction_id' => $transaction->id,
                'price' => $request->price,
                'status' => 'Pending',
                'receipt_image' => 'img/receipts/' . $filename,
                'reference_number' => $request->reference_number,
            ]);

            return redirect()->back()->with('success', 'Receipt uploaded successfully. Please wait for confirmation.');
        }

        return redirect()->back()->with('error', 'Failed to upload receipt.');
    }
}
