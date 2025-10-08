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
        // Share reminderCount dengan semua view
        View::composer('*', function ($view) {
            $now = Carbon::now();

            $reminderCount = ActionItem::where('status', 'mendatang')
                ->where('due', '>', $now)
                ->where('due', '<=', $now->copy()->addDays(7))
                ->count();

            $view->with('reminderCount', $reminderCount);
        });
    }
}
