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
            ->take(5) // Ambil 5 notifikasi terbaru
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toISOString(),
                    'created_at_human' => $notification->created_at->diffForHumans(), // ✅ TAMBAHKAN INI
                    'mom_id' => $notification->mom_id,
                    'icon' => $this->getNotificationIcon($notification->type), // ✅ TAMBAHKAN INI
                    'color' => $this->getNotificationColor($notification->type), // ✅ TAMBAHKAN INI
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

        // Redirect ke halaman detail MoM
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

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'mom_created' => 'fa-solid fa-file-circle-plus',
            'mom_updated' => 'fa-solid fa-pen-to-square',
            'mom_approved' => 'fa-solid fa-check-circle',
            'mom_rejected' => 'fa-solid fa-times-circle',
            'task_assigned' => 'fa-solid fa-tasks',
            'task_reminder' => 'fa-solid fa-clock',
            'task_completed' => 'fa-solid fa-check-circle',
            'task_overdue' => 'fa-solid fa-exclamation-triangle',
            'comment_added' => 'fa-solid fa-comment',
            'mention' => 'fa-solid fa-at',
        ];

        return $icons[$type] ?? 'fa-solid fa-bell';
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor($type)
    {
        $colors = [
            'mom_created' => 'green',
            'mom_updated' => 'blue',
            'mom_approved' => 'green',
            'mom_rejected' => 'red',
            'task_assigned' => 'yellow',
            'task_reminder' => 'orange',
            'task_completed' => 'green',
            'task_overdue' => 'red',
            'comment_added' => 'purple',
            'mention' => 'blue',
        ];

        return $colors[$type] ?? 'red';
    }
}
