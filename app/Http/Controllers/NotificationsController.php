<?php

namespace App\Http\Controllers;

class NotificationsController extends Controller
{
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return redirect()->back()->with('success', 'Notification marked as read.');
        }

        return redirect()->back()->with('failed', 'Notification not found.');
    }

    public function routeTo($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $targetUrl = $notification->data['url'] ?? $notification->data['link'] ?? route('notification.index');
            return redirect($targetUrl);
        }

        return redirect()->route('notification.index')->with('failed', 'Notification not found.');
    }
}

