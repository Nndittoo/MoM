<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\ActionItem;

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
                $now = Carbon::now();

                $reminderCount = ActionItem::where('status', 'mendatang')
                    ->where('due', '>', $now)
                    ->where('due', '<=', $now->copy()->addDays(7))
                    ->count();

                // Import NotificationController untuk getUnreadCount
                $notificationCount = \App\Http\Controllers\NotificationController::getUnreadCount();

                $view->with([
                    'reminderCount' => $reminderCount,
                    'notificationCount' => $notificationCount
                ]);
            } else {
                // Jika belum login, set ke 0
                $view->with([
                    'reminderCount' => 0,
                    'notificationCount' => 0
                ]);
            }
        });
    }
}
