<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\ActionItem;
use App\Models\AdminNotification;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share reminderCount dan notificationCount dengan semua view
        View::composer('*', function ($view) {
            // Cek apakah user sudah login
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                try {
                    $now = Carbon::now();

                    // Query reminder count - hanya ambil task yang pending
                    $reminderCount = ActionItem::where('status', 'mendatang')
                        ->where('due', '>', $now)
                        ->where('due', '<=', $now->copy()->addDays(7))
                        ->count();

                    // Query notification count
                    $notificationCount = \App\Http\Controllers\NotificationController::getUnreadCount();

                    $adminNotificationCount = 0;
                    // Hanya hitung jika user adalah admin
                    if ($user->role === 'admin') { // Asumsi Anda punya kolom 'role'
                        $adminNotificationCount = AdminNotification::where('is_read', false)->count();
                    }

                    $view->with([
                        'reminderCount' => $reminderCount,
                        'notificationCount' => $notificationCount,
                        'adminNotificationCount' => $adminNotificationCount
                    ]);
                } catch (\Exception $e) {
                    // Jika query gagal, set default 0
                    \Illuminate\Support\Facades\Log::error('ViewServiceProvider Error: ' . $e->getMessage());
                    $view->with([
                        'reminderCount' => 0,
                        'notificationCount' => 0,
                        'adminNotificationCount' => 0
                    ]);
                }
            } else {
                // Jika belum login, set ke 0
                $view->with([
                    'reminderCount' => 0,
                    'notificationCount' => 0,
                    'adminNotificationCount' => 0
                ]);
            }
        });
    }
}
