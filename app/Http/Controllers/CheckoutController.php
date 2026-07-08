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
    public function index(Request $request, Room $room = null)
    {
        if ($room && $room->exists) {
            $roomIds = [$room->id];
        } else {
            $roomIds = $request->input('room_ids', []);
        }

        if (empty($roomIds)) {
            return redirect()->route('public.rooms')->with('error', 'Please select at least one room to book.');
        }

        $check_in = $request->input('check_in');
        $check_out = $request->input('check_out');
        $guests = $request->input('guests', 1);

        $rooms = Room::query()->with(['type', 'image'])->whereIn('id', $roomIds)->get();

        if ($rooms->isEmpty()) {
            return redirect()->route('public.rooms')->with('error', 'Selected rooms not found.');
        }

        // Calculate nights
        $nights = 1;
        if ($check_in && $check_out) {
            $nights = max(1, Carbon::parse($check_in)->diffInDays(Carbon::parse($check_out)));
        }

        $totalCapacity = $rooms->sum('capacity');
        $totalPrice = $rooms->sum('price') * $nights;

        return view('public.checkout', compact('rooms', 'check_in', 'check_out', 'guests', 'nights', 'totalCapacity', 'totalPrice'));
    }

    public function process(Request $request, Room $room = null)
    {
        if ($room && $room->exists) {
            $roomIds = [$room->id];
        } else {
            $roomIds = $request->input('room_ids', []);
        }

        if (empty($roomIds)) {
            return back()->withInput()->with('error', 'Please select at least one room to book.');
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests'    => 'required|integer|min:1',
        ]);

        $rooms = Room::query()->whereIn('id', $roomIds)->get();

        if ($rooms->isEmpty()) {
            return back()->withInput()->with('error', 'Selected rooms not found.');
        }

        $totalCapacity = $rooms->sum('capacity');
        if ($totalCapacity < $request->guests) {
            return back()->withInput()->with('error', "The selected rooms only accommodate up to {$totalCapacity} guests, but you are booking for {$request->guests} guests. Please select additional rooms.");
        }

        // Check availability for EACH room using robust overlap logic
        $conflictingRoomNumbers = [];
        foreach ($rooms as $r) {
            $conflict = Transaction::query()
                ->where('room_id', $r->id)
                ->where('status', '!=', 'Canceled')
                ->where(function ($query) use ($request) {
                    $query->where('check_in', '<', $request->check_out)
                          ->where('check_out', '>', $request->check_in);
                })->exists();

            if ($conflict) {
                $conflictingRoomNumbers[] = "Room " . $r->number;
            }
        }

        if (!empty($conflictingRoomNumbers)) {
            $names = implode(', ', $conflictingRoomNumbers);
            return back()->withInput()->with('error', "Sorry, the following rooms are already booked for the selected dates: {$names}. Please choose different dates or rooms.");
        }

        // Determine if this is a new guest (to know if we need to send credentials)
        $isNewUser = !User::query()->where('email', $request->email)->exists();

        // Generate a readable temporary password for new users
        $tempPassword = $isNewUser ? Str::password(10, true, true, false) : null;

        $customerRole = Role::query()->where('name', 'Customer')->first();

        // Create or get user
        $user = User::query()->firstOrCreate(
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

        // For returning users: fix role fields if missing but do NOT reset their password
        if (!$user->role_id || !$user->role) {
            if (!$user->role && !$user->role_id) {
                $user->role    = 'Customer';
                $user->role_id = $customerRole?->id;
            } elseif (strcasecmp((string) $user->role, 'Customer') === 0 && !$user->role_id) {
                $user->role_id = $customerRole?->id;
            } elseif ($user->role_id && !$user->role && $user->userRole) {
                $user->role = $user->userRole->name;
            }
            $user->save();
        }

        // Create or get customer profile
        $customer = Customer::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'name'      => $request->name,
                'address'   => $request->phone,
                'gender'    => 'Male',
                'job'       => 'Guest',
                'birthdate' => '2000-01-01',
            ]
        );

        // Create transactions
        $transactions = new \Illuminate\Database\Eloquent\Collection();
        foreach ($rooms as $r) {
            $transactions->push(Transaction::create([
                'user_id'     => $user->id,
                'customer_id' => $customer->id,
                'room_id'     => $r->id,
                'check_in'    => $request->check_in,
                'check_out'   => $request->check_out,
                'status'      => 'Reservation',
            ]));
        }

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
        $transactions->load('room.type', 'customer.user');

        // Get payment accounts for the email
        $paymentAccounts = PaymentAccount::all();

        // Send booking confirmation email with credentials + payment accounts
        try {
            Mail::to($user->email)->send(
                new BookingConfirmationMail($transactions, $tempPassword, $paymentAccounts)
            );
        } catch (\Exception $e) {
            Log::error('Booking email failed: ' . $e->getMessage());
        }

        // Send booking confirmation SMS in parallel
        if ($user->phone) {
            $settings      = \App\Models\Setting::all()->pluck('value', 'key');
            $hotelName     = $settings['hotel_name'] ?? config('app.name');
            $checkInFormatted       = \Carbon\Carbon::parse($request->check_in)->format('d M Y');
            $checkOutFormatted      = \Carbon\Carbon::parse($request->check_out)->format('d M Y');
            $roomDetails   = $rooms->map(fn($t) => $t->number)->implode(', ');
            $smsMessage    = "Dear {$user->name}, your booking for rooms ({$roomDetails}) at {$hotelName} is confirmed.\n"
                           . "Check-in: {$checkInFormatted} | Check-out: {$checkOutFormatted}.\n"
                           . "Please upload your payment receipt to activate your booking. Thank you!";
            SmsService::send($user->phone, $smsMessage);
        }

        // Auto-login the customer and regenerate session to bind the new identity
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect to their dashboard with a clear message
        return redirect()
            ->route('dashboard.index')
            ->with('success', '🎉 Booking confirmed! Check your email for login credentials and payment instructions. Upload your receipt to activate your booking.');
    }
}
