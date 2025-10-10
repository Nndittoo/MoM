<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\Mom;

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
        // Share data dengan semua view
        View::composer('*', function ($view) {
            try {
                if (!Auth::check()) {
                    $view->with([
                        'reminderCount' => 0,
                        'notificationCount' => 0,
                        'adminNotificationCount' => 0,
                        'pendingApprovalsCount' => 0,
                        'onGoingTasksCount' => 0,
                    ]);
                    return;
                }

                $user = Auth::user();
                $now = Carbon::now();

                // USER DATA
                $reminderCount = ActionItem::where('status', 'mendatang')
                    ->where('due', '>', $now)
                    ->where('due', '<=', $now->copy()->addDays(7))
                    ->count();

                $notificationCount = Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();

                // ADMIN DATA
                $adminNotificationCount = 0;
                $pendingApprovalsCount = 0;
                $onGoingTasksCount = 0;

                // Check if user is admin
                $isAdmin = false;
                if (method_exists($user, 'hasRole')) {
                    $isAdmin = $user->hasRole('admin');
                } else {
                    $isAdmin = $user->role === 'admin';
                }

                if ($isAdmin) {
                    $adminNotificationCount = AdminNotification::where('is_read', false)->count();
                    $pendingApprovalsCount = Mom::where('status_id', 1)->count();
                    $onGoingTasksCount = ActionItem::where('status', 'mendatang')
                        ->whereHas('mom', function($q) {
                            $q->where('status_id', 2);
                        })
                        ->count();
                }

                $view->with([
                    'reminderCount' => $reminderCount,
                    'notificationCount' => $notificationCount,
                    'adminNotificationCount' => $adminNotificationCount,
                    'pendingApprovalsCount' => $pendingApprovalsCount,
                    'onGoingTasksCount' => $onGoingTasksCount,
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('ViewServiceProvider Error: ' . $e->getMessage());
                $view->with([
                    'reminderCount' => 0,
                    'notificationCount' => 0,
                    'adminNotificationCount' => 0,
                    'pendingApprovalsCount' => 0,
                    'onGoingTasksCount' => 0,
                ]);
            }
        });
    }
}
