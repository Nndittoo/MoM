<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ActionItem;
use App\Http\Controllers\NotificationController;

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
        // Share reminderCount dan notificationCount ke semua view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $now = Carbon::now();
                $userId = Auth::id();

                $reminderCount = ActionItem::where('status', 'mendatang')
                    ->where('due', '>', $now)
                    ->where('due', '<=', $now->copy()->addDays(7))
                    ->where(function ($q) use ($userId) {
                        $q->whereHas('mom', function ($query) use ($userId) {
                            // Sesuaikan nama kolom pembuat MoM
                            $query->where('creator_id', $userId);
                        })
                        // Jika ActionItem juga punya kolom pembuat, tambahkan di sini
                        ->orWhere('creator_id', $userId);
                    })
                    ->count();

                // Hitung notifikasi belum dibaca
                $notificationCount = NotificationController::getUnreadCount();

                $view->with([
                    'reminderCount' => $reminderCount,
                    'notificationCount' => $notificationCount,
                ]);
            } else {
                // Jika belum login
                $view->with([
                    'reminderCount' => 0,
                    'notificationCount' => 0,
                ]);
            }
        });
    }
}
