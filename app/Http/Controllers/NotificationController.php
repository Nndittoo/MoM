<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = Notification::with('mom')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.notifications', compact('notifications'));
    }

    /**
     * Get unread notifications count
     */
    public static function getUnreadCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        /** @var User $user */
        $user = Auth::user();

        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecent()
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = Notification::with('mom')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toISOString(),
                    'mom_id' => $notification->mom_id,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => self::getUnreadCount()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        // PERBAIKI LOGIKA REDIRECT:
        // Arahkan ke halaman detail MoM menggunakan mom_id dari notifikasi.
        return redirect()->route('moms.detail', ['mom' => $notification->mom_id]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Create notification (static helper method)
     */
    public static function createNotification($userId, $momId, $type, $title, $message)
    {
        return Notification::create([
            'user_id' => $userId,
            'mom_id' => $momId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false
        ]);
    }
}
