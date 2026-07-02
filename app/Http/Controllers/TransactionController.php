<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
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

    public function cancel(Transaction $transaction)
    {
        if (auth()->user()->isSuperAdmin() || $transaction->status == 'Reservation') {
            $transaction->status = 'Canceled';
            $transaction->save();
            activity()->causedBy(auth()->user())->log("Booking #{$transaction->id} canceled");
            return redirect()->back()->with('success', 'Booking canceled successfully.');
        }
        return redirect()->back()->with('failed', 'Cannot cancel this booking.');
    }

    public function edit(Transaction $transaction)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can edit bookings.');
        }

        $customers = Customer::orderBy('name')->get();
        $rooms = Room::orderBy('number')->get();

        return view('transaction.edit', compact('transaction', 'customers', 'rooms'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update bookings.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status' => 'required|string',
        ]);

        $transaction->update([
            'customer_id' => $request->customer_id,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
        ]);

        activity()->causedBy(auth()->user())->log("Booking #{$transaction->id} updated by Super Admin");

        return redirect()->route('transaction.index')->with('success', "Booking #{$transaction->id} updated successfully.");
    }

    public function destroy(Transaction $transaction)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete bookings.');
        }

        $id = $transaction->id;

        foreach ($transaction->payment as $payment) {
            if ($payment->receipt_image && file_exists(public_path($payment->receipt_image))) {
                @unlink(public_path($payment->receipt_image));
            }
        }
        $transaction->payment()->delete();
        $transaction->delete();

        activity()->causedBy(auth()->user())->log("Booking #{$id} deleted by Super Admin");

        return redirect()->route('transaction.index')->with('success', "Booking #{$id} deleted permanently.");
    }

    public function uploadReceipt(Request $request, Transaction $transaction)
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
