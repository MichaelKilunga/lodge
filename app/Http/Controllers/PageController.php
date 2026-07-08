<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\NewsletterSubscriber;

class PageController extends Controller
{
    public function home()
    {
        $rooms = Room::with(['type', 'image'])->inRandomOrder()->take(3)->get();
        $facilities = \App\Models\Facility::take(3)->get();
        return view('public.home', compact('rooms', 'facilities'));
    }

    public function rooms(Request $request)
    {
        $check_in = $request->input('check_in');
        $check_out = $request->input('check_out');
        $guests = $request->input('guests');

        $query = Room::with(['type', 'image']);

        if ($check_in && $check_out) {
            $conflictingRoomIds = \App\Models\Transaction::query()->where('status', '!=', 'Canceled')
                ->where(function ($q) use ($check_in, $check_out) {
                    $q->where('check_in', '<', $check_out)
                      ->where('check_out', '>', $check_in);
                })
                ->pluck('room_id')
                ->unique();

            $query->whereNotIn('id', $conflictingRoomIds);
        }

        $rooms = $query->get();

        return view('public.rooms', compact('rooms', 'check_in', 'check_out', 'guests'));
    }

    public function room(Room $room)
    {
        $room->load(['type', 'image']);
        return view('public.room_details', compact('room'));
    }

    public function location()
    {
        return view('public.location');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        NewsletterSubscriber::create(['email' => $request->email]);

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
