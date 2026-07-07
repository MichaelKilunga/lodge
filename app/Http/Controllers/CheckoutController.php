<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\PaymentAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingConfirmationMail;
use App\Models\MarketingTrafficLog;
use App\Services\SmsService;

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

        $customerRole = Role::where('name', 'Customer')->first();

        // Create or get user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name'       => $request->name,
                'phone'      => $request->phone,
                'password'   => Hash::make($tempPassword ?? Str::random(12)),
                'role'       => 'Customer',
                'role_id'    => $customerRole?->id,
                'random_key' => Str::random(60),
            ]
        );

        // Always keep the phone number up-to-date for returning guests
        if (!$user->phone && $request->phone) {
            $user->phone = $request->phone;
            $user->save();
        }

        // Ensure returning users or users missing role attributes have them updated
        if (!$isNewUser || !$user->role_id || !$user->role) {
            if (!$isNewUser) {
                $tempPassword = Str::password(10, true, true, false);
                $user->password = Hash::make($tempPassword);
            }
            if (!$user->role && !$user->role_id) {
                $user->role = 'Customer';
                $user->role_id = $customerRole?->id;
            } elseif (strcasecmp((string) $user->role, 'Customer') === 0 && !$user->role_id) {
                $user->role = 'Customer';
                $user->role_id = $customerRole?->id;
            } elseif ($user->role_id && !$user->role && $user->userRole) {
                $user->role = $user->userRole->name;
            }
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

        try {
            MarketingTrafficLog::create([
                'session_id'  => $request->session()->getId() ?: md5($request->ip() . '_' . date('Y-m-d')),
                'ip_address'  => $request->ip(),
                'url'         => substr($request->fullUrl(), 0, 255),
                'page_type'   => 'Checkout',
                'referrer'    => substr((string)$request->headers->get('referer'), 0, 500),
                'source'      => $request->get('utm_source') ? ucfirst((string)$request->get('utm_source')) : 'Website Direct',
                'device_type' => 'Desktop',
                'event_type'  => 'booking_completed',
            ]);
        } catch (\Exception $e) { }

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
            Log::error('Booking email failed: ' . $e->getMessage());
        }

        // Send booking confirmation SMS in parallel
        if ($user->phone) {
            $settings      = \App\Models\Setting::all()->pluck('value', 'key');
            $hotelName     = $settings['hotel_name'] ?? config('app.name');
            $checkIn       = \Carbon\Carbon::parse($transaction->check_in)->format('d M Y');
            $checkOut      = \Carbon\Carbon::parse($transaction->check_out)->format('d M Y');
            $smsMessage    = "Dear {$user->name}, your room booking at {$hotelName} is confirmed.\n"
                           . "Room: {$transaction->room->number} | Check-in: {$checkIn} | Check-out: {$checkOut}.\n"
                           . "Please upload your payment receipt to activate your booking. Thank you!";
            SmsService::send($user->phone, $smsMessage);
        }

        // Auto-login the customer
        Auth::login($user);

        // Redirect to their dashboard with a clear message
        return redirect()
            ->route('dashboard.index')
            ->with('success', '🎉 Booking confirmed! Check your email for login credentials and payment instructions. Upload your receipt to activate your booking.');
    }
}
