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

        // Ambil MoM yang baru dibuat (3 terakhir)
        $newMoms = Mom::with('creator')
                     ->orderBy('created_at', 'desc')
                     ->take(3)
                     ->get()
                     ->map(function($mom) {
                         return [
                             'type' => 'mom_created',
                             'title' => "MoM #{$mom->version_id} Created",
                             'subtitle' => $mom->title,
                             'creator' => $mom->creator->name ?? 'Unknown',
                             'date' => $mom->created_at,
                             'icon' => 'fa-file-alt',
                             'color' => 'blue'
                         ];
                     });

        // Ambil Action Item yang baru dibuat (3 terakhir)
        $newTasks = ActionItem::with('mom')
                              ->orderBy('created_at', 'desc')
                              ->take(3)
                              ->get()
                              ->map(function($task) {
                                  return [
                                      'type' => 'task_created',
                                      'title' => 'New Task Added',
                                      'subtitle' => Str::limit($task->item, 40),
                                      'mom_reference' => 'MoM #' . ($task->mom->version_id ?? $task->mom_id),
                                      'date' => $task->created_at,
                                      'icon' => 'fa-tasks',
                                      'color' => 'green'
                                  ];
                              });

        // Gabungkan dan urutkan berdasarkan waktu terbaru
        return $activities->merge($newMoms)
                         ->merge($newTasks)
                         ->sortByDesc('date')
                         ->take(4)
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

        // Search by title, pimpinan_rapat, notulen, atau location
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('pimpinan_rapat', 'like', '%' . $search . '%')
                  ->orWhere('notulen', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_id', $request->status);
        }

        $results = $query->orderBy('created_at', 'desc')->take(10)->get();

        // Format response sesuai dengan yang diharapkan frontend
        return response()->json($results->map(function($mom) {
            return [
                'version_id' => $mom->version_id,
                'title' => $mom->title,
                'status_id' => $mom->status_id,
                'created_at' => $mom->created_at->toISOString(), // Format ISO untuk parsing JavaScript
            ];
        }));
    }
}
