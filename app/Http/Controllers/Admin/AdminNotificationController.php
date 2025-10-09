<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Menampilkan halaman notifikasi admin
     */
    public function index()
    {
        $notifications = AdminNotification::orderBy('created_at', 'desc')->get();
        return view('admin.notification', compact('notifications'));
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca dan mengarahkan ke halaman relevan
     */
    public function read($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true]);

        // Logika redirect berdasarkan tipe notifikasi
        switch ($notification->type) {
            case 'mom_pending':
                // Arahkan ke halaman detail MoM yang perlu di-approve
                return redirect()->route('admin.details', $notification->related_id);
            case 'user_new':
                // Arahkan ke halaman manajemen user
                return redirect()->route('admin.users');
            case 'task_urgent':
                 // Arahkan ke halaman daftar tugas
                return redirect()->route('admin.task');
            default:
                return redirect()->route('admin.notification');
        }
    }

    public function getRecent()
    {
        $notifications = AdminNotification::latest() // Mengurutkan berdasarkan 'created_at' (terbaru dulu)
            ->take(5) // Ambil 5 notifikasi teratas
            ->get()
            ->map(function($notification) {
                // Kita format datanya agar mudah digunakan di JavaScript
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at_human' => $notification->created_at->diffForHumans(),
                    'icon' => $notification->icon, // Ambil dari accessor di model
                    'color' => $notification->color, // Ambil dari accessor di model
                    'url' => route('admin.notification.read', $notification->id)
                ];
            });

        $unreadCount = AdminNotification::where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Helper statis untuk membuat notifikasi dari controller lain
     */
    public static function createNotification($type, $title, $message, $relatedId = null)
    {
        AdminNotification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
            'is_read' => false
        ]);
    }
}
