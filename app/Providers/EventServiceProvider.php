<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ActionItem;
use App\Observers\ActionItemObserver;

class EventServiceProvider extends ServiceProvider
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
        //
        ActionItem::observe(ActionItemObserver::class);
    }
}
