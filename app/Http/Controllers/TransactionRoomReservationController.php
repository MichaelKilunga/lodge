<?php

namespace App\Http\Controllers;

use App\Events\NewReservationEvent;
use App\Events\RefreshDashboardEvent;
use App\Helpers\Helper;
use App\Http\Requests\ChooseRoomRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewRoomReservationDownPayment;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\PaymentRepositoryInterface;
use App\Repositories\Interface\ReservationRepositoryInterface;
use App\Repositories\Interface\TransactionRepositoryInterface;
use App\Services\SmsService;
use Illuminate\Http\Request;

class TransactionRoomReservationController extends Controller
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function pickFromCustomer(Request $request, CustomerRepositoryInterface $customerRepository)
    {
        $customers = $customerRepository->get($request);
        $customersCount = $customerRepository->count($request);

        return view('transaction.reservation.pickFromCustomer', [
            'customers' => $customers,
            'customersCount' => $customersCount,
        ]);
    }

    public function createIdentity()
    {
        return view('transaction.reservation.createIdentity');
    }

    public function storeCustomer(StoreCustomerRequest $request, CustomerRepositoryInterface $customerRepository)
    {
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'name' => $user->name,
                    'address' => $request->address,
                    'job' => $request->job,
                    'birthdate' => $request->birthdate,
                    'gender' => $request->gender,
                    'user_id' => $user->id,
                ]);
            }
            if ($request->phone && !$user->phone) {
                $user->phone = $request->phone;
                $user->save();
            }
            return redirect()
                ->route('transaction.reservation.viewCountPerson', ['customer' => $customer->id])
                ->with('success', 'Existing customer '.$customer->name.' selected!');
        }

        $customer = $customerRepository->store($request);

        return redirect()
            ->route('transaction.reservation.viewCountPerson', ['customer' => $customer->id])
            ->with('success', 'Customer '.$customer->name.' created!');
    }

    public function viewCountPerson(Customer $customer)
    {
        return view('transaction.reservation.viewCountPerson', [
            'customer' => $customer,
        ]);
    }

    public function chooseRoom(ChooseRoomRequest $request, Customer $customer)
    {
        $stayFrom = $request->check_in;
        $stayUntil = $request->check_out;

        $selectedRoomIds = $request->input('selected_rooms') ? explode(',', $request->input('selected_rooms')) : [];

        $occupiedRoomId = $this->getOccupiedRoomID($request->check_in, $request->check_out);
        if (!empty($selectedRoomIds)) {
            $occupiedRoomId = $occupiedRoomId->merge($selectedRoomIds);
        }

        $rooms = $this->reservationRepository->getUnocuppiedroom($request, $occupiedRoomId);
        $roomsCount = $this->reservationRepository->countUnocuppiedroom($request, $occupiedRoomId);

        $selectedRooms = \App\Models\Room::with('type')->whereIn('id', $selectedRoomIds)->get();
        $currentCapacity = $selectedRooms->sum('capacity');

        return view('transaction.reservation.chooseRoom', [
            'customer' => $customer,
            'rooms' => $rooms,
            'stayFrom' => $stayFrom,
            'stayUntil' => $stayUntil,
            'roomsCount' => $roomsCount,
            'selectedRooms' => $selectedRooms,
            'currentCapacity' => $currentCapacity,
            'selectedRoomsString' => $request->input('selected_rooms', ''),
        ]);
    }

    public function confirmation(Customer $customer, $room, $stayFrom, $stayUntil)
    {
        $roomIds = explode(',', $room);
        $rooms = Room::with('type')->whereIn('id', $roomIds)->get();

        $totalPrice = 0;
        $totalCapacity = 0;
        foreach ($rooms as $r) {
            $totalPrice += $r->price;
            $totalCapacity += $r->capacity;
        }

        $dayDifference = Helper::getDateDifference($stayFrom, $stayUntil);
        $downPayment = ($totalPrice * $dayDifference) * 0.15;

        return view('transaction.reservation.confirmation', [
            'customer' => $customer,
            'rooms' => $rooms,
            'roomIdsString' => $room,
            'stayFrom' => $stayFrom,
            'stayUntil' => $stayUntil,
            'downPayment' => $downPayment,
            'dayDifference' => $dayDifference,
            'totalPrice' => $totalPrice,
            'totalCapacity' => $totalCapacity,
        ]);
    }

    public function payDownPayment(
        Customer $customer,
        $room,
        Request $request,
        TransactionRepositoryInterface $transactionRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $roomIds = explode(',', $room);
        $rooms = Room::whereIn('id', $roomIds)->get();

        $totalPrice = 0;
        foreach ($rooms as $r) {
            $totalPrice += $r->price;
        }

        $dayDifference = Helper::getDateDifference($request->check_in, $request->check_out);
        $minimumDownPayment = ($totalPrice * $dayDifference) * 0.15;

        $request->validate([
            'downPayment' => 'required|numeric|gte:'.$minimumDownPayment,
        ]);

        $occupiedRoomId = $this->getOccupiedRoomID($request->check_in, $request->check_out);
        $occupiedRoomIdInArray = $occupiedRoomId->toArray();

        foreach ($rooms as $r) {
            if (in_array($r->id, $occupiedRoomIdInArray)) {
                return redirect()->back()->with('failed', 'Sorry, room '.$r->number.' already occupied');
            }
        }

        $firstTransaction = null;
        foreach ($rooms as $r) {
            $transaction = $transactionRepository->store($request, $customer, $r);
            if (!$firstTransaction) {
                $firstTransaction = $transaction;
            }
        }

        $status = 'Down Payment';
        $payment = $paymentRepository->store($request, $firstTransaction, $status);

        $superAdmins = User::where('role', 'Super')->get();

        foreach ($superAdmins as $superAdmin) {
            $message = 'Reservation added by '.$customer->name;
            try {
                event(new NewReservationEvent($message, $superAdmin));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Broadcasting failed for NewReservationEvent: ' . $e->getMessage());
            }

            try {
                $superAdmin->notify(new NewRoomReservationDownPayment($firstTransaction, $payment));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notification failed for NewRoomReservationDownPayment: ' . $e->getMessage());
            }
        }

        // Send SMS to admin recipient in parallel with the in-app notification
        $adminPhone = \App\Models\Setting::where('key', 'admin_sms_recipient')->value('value');
        if ($adminPhone) {
            $hotelName  = \App\Models\Setting::where('key', 'hotel_name')->value('value') ?? config('app.name');
            $checkIn    = \Carbon\Carbon::parse($firstTransaction->check_in)->format('d M Y');
            $checkOut   = \Carbon\Carbon::parse($firstTransaction->check_out)->format('d M Y');
            $roomNumbers = $rooms->pluck('number')->implode(', ');
            $smsText    = "New reservation at {$hotelName} by {$customer->name}.\n"
                        . "Rooms: {$roomNumbers} | Check-in: {$checkIn} | Check-out: {$checkOut}.\n"
                        . 'Down payment recorded. Please review.';
            try {
                SmsService::send($adminPhone, $smsText);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('SMS sending failed: ' . $e->getMessage());
            }
        }

        try {
            event(new RefreshDashboardEvent('Someone reserved a room'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed for RefreshDashboardEvent: ' . $e->getMessage());
        }

        $roomNumbersList = $rooms->pluck('number')->implode(', ');
        return redirect()->route('transaction.index')
            ->with('success', 'Rooms '.$roomNumbersList.' have been reservated by '.$customer->name);
    }

    private function getOccupiedRoomID($stayFrom, $stayUntil)
    {
        return Transaction::where([['check_in', '<=', $stayFrom], ['check_out', '>=', $stayUntil]])
            ->orWhere([['check_in', '>=', $stayFrom], ['check_in', '<=', $stayUntil]])
            ->orWhere([['check_out', '>=', $stayFrom], ['check_out', '<=', $stayUntil]])
            ->pluck('room_id');
    }
}
