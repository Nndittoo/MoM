<?php

namespace App\Http\Controllers;

use App\Models\Mom;
use App\Models\ActionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Cards
        $stats = [
            'approved' => Mom::where('status_id', 2)->count(),
            'pending' => Mom::where('status_id', 1)->count(),
            'tasks_due' => ActionItem::where('status', 'mendatang')->count(), // Semua task mendatang
            'tasks_completed' => ActionItem::where('status', 'selesai')->count(), // Semua task selesai
        ];

        // Recent MoMs untuk tabel
        $recentMoms = Mom::with(['status', 'creator'])
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();

        // Recent Activity
        $recentActivity = $this->getRecentActivity();

        // Chart Data
        $chartData = $this->getChartData();

        return view('user.dashboard', compact('stats', 'recentMoms', 'recentActivity', 'chartData'));
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Ambil MoM yang baru diapprove (2 terakhir)
        $approvedMoms = Mom::where('status_id', 2)
                          ->orderBy('updated_at', 'desc')
                          ->take(2)
                          ->get()
                          ->map(function($mom) {
                              return [
                                  'type' => 'approved',
                                  'title' => "MoM #{$mom->version_id} Approved",
                                  'subtitle' => $mom->title,
                                  'date' => $mom->updated_at,
                                  'icon' => 'fa-check',
                                  'color' => 'green'
                              ];
                          });

        // Ambil task yang hampir deadline (2 terdekat)
        $dueTasks = ActionItem::with('mom')
                              ->where('due', '>=', Carbon::now())
                              ->where('due', '<=', Carbon::now()->addDays(7))
                              ->where('status', 'mendatang')
                              ->orderBy('due', 'asc')
                              ->take(2)
                              ->get()
                              ->map(function($task) {
                                  return [
                                      'type' => 'task_due',
                                      'title' => 'Task "' . Str::limit($task->item, 30) . '"',
                                      'subtitle' => 'From MoM #' . $task->mom_id,
                                      'date' => $task->due,
                                      'icon' => 'fa-hourglass-half',
                                      'color' => 'yellow'
                                  ];
                              });

        // Ambil MoM yang baru dibuat (1 terakhir)
        $newMoms = Mom::orderBy('created_at', 'desc')
                     ->take(1)
                     ->get()
                     ->map(function($mom) {
                         return [
                             'type' => 'created',
                             'title' => "New MoM #{$mom->version_id} Created",
                             'subtitle' => $mom->title,
                             'date' => $mom->created_at,
                             'icon' => 'fa-plus',
                             'color' => 'red'
                         ];
                     });

        return $activities->merge($approvedMoms)
                         ->merge($dueTasks)
                         ->merge($newMoms)
                         ->sortByDesc('date')
                         ->take(5)
                         ->values();
    }

    private function getChartData()
    {
        return [
            'week' => $this->getWeeklyData(),
            'month' => $this->getMonthlyData(),
            'year' => $this->getYearlyData(),
        ];
    }

    private function getWeeklyData()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $days = [];
        $approved = [];
        $pending = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $days[] = $date->format('D');

            $approved[] = Mom::where('status_id', 2)
                            ->whereDate('created_at', $date)
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->whereDate('created_at', $date)
                           ->count();
        }

        return [
            'categories' => $days,
            'series' => [
                ['name' => 'Approved', 'data' => $approved],
                ['name' => 'Pending', 'data' => $pending]
            ]
        ];
    }

    private function getMonthlyData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $weeks = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $approved = [];
        $pending = [];

        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startOfMonth->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->addWeek();

            $approved[] = Mom::where('status_id', 2)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->whereBetween('created_at', [$weekStart, $weekEnd])
                           ->count();
        }

        return [
            'categories' => $weeks,
            'series' => [
                ['name' => 'Approved', 'data' => $approved],
                ['name' => 'Pending', 'data' => $pending]
            ]
        ];
    }

    private function getYearlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $approved = [];
        $pending = [];

        for ($i = 1; $i <= 12; $i++) {
            $approved[] = Mom::where('status_id', 2)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->whereMonth('created_at', $i)
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->whereYear('created_at', Carbon::now()->year)
                           ->whereMonth('created_at', $i)
                           ->count();
        }

        return [
            'categories' => $months,
            'series' => [
                ['name' => 'Approved', 'data' => $approved],
                ['name' => 'Pending', 'data' => $pending]
            ]
        ];
    }

    // API endpoint untuk filter search
    public function searchMoms(Request $request)
    {
        $query = Mom::with(['status', 'creator']);

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status_id', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->take(10)->get());
    }
}
