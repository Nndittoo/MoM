<?php

namespace App\Providers;

use Illuminate\Support\Facades\View; 
use Illuminate\Support\ServiceProvider;
use App\Models\Mom; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer untuk Admin Sidebar
        View::composer([
            
            'admin.dashboard',
            'admin.approvals',
            'admin.mom',          
            'admin.users',
            'admin.task',
            'admin.notification',
            'admin.shows',
            'admin.create', 
            'admin.calendars',
            'admin.moms.edit',
            'admin.details',
            'admin.edit',
           
        ], function ($view) {
            // Hitung MoM yang berstatus "Menunggu" 
            $pendingApprovalsCount = Mom::where('status_id', 1)->count();

            // Bagikan variabel ini ke view yang didaftarkan
            $view->with('pendingApprovalsCount', $pendingApprovalsCount);
        });
    }
}