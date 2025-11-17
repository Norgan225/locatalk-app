<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the current user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Notification::where('user_id', $user->id);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->has('read')) {
            $isRead = $request->boolean('read');
            if ($isRead) {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        $notifications = $query->orderByDesc('created_at')->paginate(50);

        if ($request->wantsJson()) {
            return response()->json($notifications, 200);
        }

        return view('notifications.index', ['notifications' => $notifications]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        if ($request->wantsJson()) {
            return response()->json(['count' => $count], 200);
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Notification marquée comme lue.',
                'data' => $notification
            ], 200);
        }

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.'], 200);
        }

        return redirect()->back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Notification supprimée avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Notification supprimée.');
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(Request $request)
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->whereNotNull('read_at')
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Notifications lues supprimées avec succès.'], 200);
        }

        return redirect()->back()->with('success', 'Notifications lues supprimées.');
    }
}
