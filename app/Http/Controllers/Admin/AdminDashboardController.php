<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use App\Models\User;
use App\Models\ActionItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Main Statistics
        $stats = [
            'pending' => Mom::where('status_id', 1)->count(),
            'approved' => Mom::where('status_id', 2)->count(),
            'rejected' => Mom::where('status_id', 3)->count(),
            'active_users' => User::count(),
        ];

        // Chart Data - Weekly Activity (7 hari terakhir)
        $weeklyData = $this->getWeeklyActivity();

        // Chart Data - Status Breakdown (Donut Chart)
        $statusBreakdown = [
            'approved' => $stats['approved'],
            'pending' => $stats['pending'],
            'rejected' => $stats['rejected'],
        ];

        // Pending Approvals (MoM yang menunggu persetujuan) - Latest 5
        $pendingApprovals = Mom::with(['creator'])
                              ->where('status_id', 1)
                              ->orderBy('created_at', 'desc')
                              ->take(5)
                              ->get();

        // Most Active Users (User yang paling banyak membuat MoM bulan ini)
        $activeUsers = DB::table('users')
                        ->select('users.id', 'users.name', DB::raw('COUNT(moms.version_id) as created_moms_count'))
                        ->join('moms', 'users.id', '=', 'moms.creator_id')
                        ->whereMonth('moms.created_at', Carbon::now()->month)
                        ->whereYear('moms.created_at', Carbon::now()->year)
                        ->groupBy('users.id', 'users.name')
                        ->orderByDesc('created_moms_count')
                        ->limit(5)
                        ->get();

        return view('admin.dashboard', compact(
            'stats',
            'weeklyData',
            'statusBreakdown',
            'pendingApprovals',
            'activeUsers'
        ));
    }

    /**
     * Get weekly activity data (7 hari terakhir)
     */
    private function getWeeklyActivity()
    {
        $days = [];
        $created = [];
        $approved = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->translatedFormat('D'); // Sen, Sel, Rab, dll

            // MoM yang dibuat pada hari ini
            $created[] = Mom::whereDate('created_at', $date->format('Y-m-d'))->count();

            // MoM yang disetujui pada hari ini (berdasarkan updated_at)
            $approved[] = Mom::where('status_id', 2)
                            ->whereDate('updated_at', $date->format('Y-m-d'))
                            ->count();
        }

        return [
            'categories' => $days,
            'series' => [
                ['name' => 'MoM Dibuat', 'data' => $created],
                ['name' => 'MoM Disetujui', 'data' => $approved]
            ]
        ];
    }
}
