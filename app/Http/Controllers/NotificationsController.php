<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tab = $request->query('tab', 'all');

        $unreadCount = $user->unreadNotifications()->count();
        $readCount = $user->readNotifications()->count();
        $totalCount = $user->notifications()->count();

        if ($tab === 'unread') {
            $notifications = $user->unreadNotifications()->latest()->paginate(15);
        } elseif ($tab === 'read') {
            $notifications = $user->readNotifications()->latest()->paginate(15);
        } else {
            $notifications = $user->notifications()->latest()->paginate(15);
        }

        return view('notification.index', compact(
            'notifications',
            'unreadCount',
            'readCount',
            'totalCount',
            'tab'
        ));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Route to notification destination target URL safely.
     */
    public function routeTo($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $url = $notification->data['url'] ?? route('notification.index');
            return redirect($url);
        }

        return redirect()->route('notification.index')->with('failed', 'Notification not found or may have been deleted.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification deleted.']);
        }

        return redirect()->back()->with('success', 'Notification removed.');
    }

    /**
     * Clear all notifications.
     */
    public function destroyAll()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications cleared.']);
        }

        return redirect()->route('notification.index')->with('success', 'All notifications cleared.');
    }
}

