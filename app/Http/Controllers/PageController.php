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

    public function rooms()
    {
        $rooms = Room::with(['type', 'image'])->get();
        return view('public.rooms', compact('rooms'));
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
