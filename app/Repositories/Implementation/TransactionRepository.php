<?php

namespace App\Repositories\Implementation;

use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function store($request, Customer $customer, Room $room)
    {
        return Transaction::create([
            'user_id'     => auth()->user()->id,
            'customer_id' => $customer->id,
            'room_id'     => $room->id,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
            'status'      => 'Reservation',
        ]);
    }

    public function getTransaction($request)
    {
        return Transaction::with(['user', 'room.type', 'customer', 'payment.user'])
            ->where('check_out', '>=', Carbon::now())
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', '=', $request->search)
                      ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $request->search . '%'));
                });
            })
            ->when(! empty($request->filter_status), function ($query) use ($request) {
                $query->where('status', $request->filter_status);
            })
            ->when(! empty($request->filter_date_from), function ($query) use ($request) {
                $query->whereDate('check_in', '>=', $request->filter_date_from);
            })
            ->when(! empty($request->filter_date_to), function ($query) use ($request) {
                $query->whereDate('check_in', '<=', $request->filter_date_to);
            })
            ->orderBy('check_out', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate(20)
            ->appends($request->all());
    }

    public function getTransactionExpired($request)
    {
        return Transaction::with(['user', 'room.type', 'customer', 'payment.user'])
            ->where('check_out', '<', Carbon::now())
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', '=', $request->search)
                      ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $request->search . '%'));
                });
            })
            ->when(! empty($request->filter_status), function ($query) use ($request) {
                $query->where('status', $request->filter_status);
            })
            ->when(! empty($request->filter_date_from), function ($query) use ($request) {
                $query->whereDate('check_in', '>=', $request->filter_date_from);
            })
            ->when(! empty($request->filter_date_to), function ($query) use ($request) {
                $query->whereDate('check_in', '<=', $request->filter_date_to);
            })
            ->orderBy('check_out', 'ASC')
            ->paginate(20)
            ->appends($request->all());
    }
}
