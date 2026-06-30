<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\PaymentAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;

class CheckoutController extends Controller
{
    public function index(Room $room)
    {
        $room->load(['type', 'image']);
        return view('public.checkout', compact('room'));
    }

    public function process(Request $request, Room $room)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests'    => 'required|integer|min:1|max:' . $room->capacity,
        ]);

        // Check availability
        $conflictingTransactions = Transaction::where('room_id', $room->id)
            ->whereNotIn('status', ['Canceled'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
            })->exists();

        if ($conflictingTransactions) {
            return back()->with('error', 'Sorry, this room is already booked for the selected dates. Please choose different dates.');
        }

        // Determine if this is a new guest (to know if we need to send credentials)
        $isNewUser = !User::where('email', $request->email)->exists();

        // Generate a readable temporary password for new users
        $tempPassword = $isNewUser ? Str::password(10, true, true, false) : null;

        // Create or get user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name'       => $request->name,
                'password'   => Hash::make($tempPassword ?? Str::random(12)),
                'role'       => 'Customer',
                'random_key' => Str::random(60),
            ]
        );

        // If returning user, generate a fresh temp password and update it
        if (!$isNewUser) {
            $tempPassword = Str::password(10, true, true, false);
            $user->password = Hash::make($tempPassword);
            $user->save();
        }

        // Create or get customer profile
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name'      => $request->name,
                'address'   => $request->phone,
                'gender'    => 'Male',
                'job'       => 'Guest',
                'birthdate' => '2000-01-01',
            ]
        );

        // Create transaction
        $transaction = Transaction::create([
            'user_id'     => $user->id,
            'customer_id' => $customer->id,
            'room_id'     => $room->id,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
            'status'      => 'Reservation',
        ]);

        // Load relationships for the email
        $transaction->load('room.type', 'customer.user');

        // Get payment accounts for the email
        $paymentAccounts = PaymentAccount::all();

        // Send booking confirmation email with credentials + payment accounts
        try {
            Mail::to($user->email)->send(
                new BookingConfirmationMail($transaction, $tempPassword, $paymentAccounts)
            );
        } catch (\Exception $e) {
            \Log::error('Booking email failed: ' . $e->getMessage());
        }

        // Auto-login the customer
        Auth::login($user);

        // Redirect to their dashboard with a clear message
        return redirect()
            ->route('dashboard.index')
            ->with('success', '🎉 Booking confirmed! Check your email for login credentials and payment instructions. Upload your receipt to activate your booking.');
    }
}
