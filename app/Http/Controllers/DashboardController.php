<?php

namespace App\Http\Controllers;

use App\Models\Mom;
use App\Models\ActionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Statistik Cards
        $stats = [
            'approved' => Mom::where('status_id', 2)->where('creator_id', $userId)->count(),
            'pending' => Mom::where('status_id', 1)->where('creator_id', $userId)->count(),
            'tasks_due' => ActionItem::whereHas('mom', function ($q) use ($userId) {
                                $q->where('creator_id', $userId);
                            })->where('status', 'mendatang')->count(),
            'tasks_completed' => ActionItem::whereHas('mom', function ($q) use ($userId) {
                                        $q->where('creator_id', $userId);
                                    })->where('status', 'selesai')->count(),
        ];

        // Recent MoMs untuk tabel
        $recentMoms = Mom::with(['status', 'creator'])
                        ->where('creator_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();

        // Recent Activity
        $recentActivity = $this->getRecentActivity($userId);

        // Chart Data
        $chartData = $this->getChartData($userId);

        return view('user.dashboard', compact('stats', 'recentMoms', 'recentActivity', 'chartData'));
    }

    private function getRecentActivity($userId)
    {
        $activities = collect();

        // Ambil MoM yang baru dibuat (3 terakhir)
        $newMoms = Mom::with('creator')
                     ->where('creator_id', $userId)
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
                              ->whereHas('mom', function ($q) use ($userId) {
                                  $q->where('creator_id', $userId);
                              })
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

    private function getChartData($userId)
    {
        return [
            'week' => $this->getWeeklyData($userId),
            'month' => $this->getMonthlyData($userId),
            'year' => $this->getYearlyData($userId),
        ];
    }

    private function getWeeklyData($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $days = [];
        $approved = [];
        $pending = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $days[] = $date->format('D');

            $approved[] = Mom::where('status_id', 2)
                            ->where('creator_id', $userId)
                            ->whereDate('created_at', $date)
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->where('creator_id', $userId)
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

    private function getMonthlyData($userId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $weeks = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $approved = [];
        $pending = [];

        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startOfMonth->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->addWeek();

            $approved[] = Mom::where('status_id', 2)
                            ->where('creator_id', $userId)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->where('creator_id', $userId)
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

    private function getYearlyData($userId)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $approved = [];
        $pending = [];

        for ($i = 1; $i <= 12; $i++) {
            $approved[] = Mom::where('status_id', 2)
                            ->where('creator_id', $userId)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->whereMonth('created_at', $i)
                            ->count();

            $pending[] = Mom::where('status_id', 1)
                           ->where('creator_id', $userId)
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
        $query = $request->input('q') ?? $request->input('search');

        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        $userId = Auth::id();

        $results = Mom::with(['status', 'creator'])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                ->orWhere('pembahasan', 'like', '%' . $query . '%')
                ->orWhere('pimpinan_rapat', 'like', '%' . $query . '%')
                ->orWhere('notulen', 'like', '%' . $query . '%')
                ->orWhere('location', 'like', '%' . $query . '%');
            })
            ->where(function($q) use ($userId) {
                // MoM milik user sendiri (semua status)
                $q->where('creator_id', $userId)
                // ATAU MoM dari user lain yang sudah disetujui
                ->orWhereHas('status', function($sq) {
                    $sq->where('status', 'Disetujui');
                });
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Format response untuk frontend
        return response()->json($results->map(function($mom) {
            return [
                'version_id' => $mom->version_id,
                'title' => $mom->title,
                'location' => $mom->location,
                'created_at' => $mom->created_at->toISOString(),
                'status' => $mom->status->status ?? 'Unknown',
                'creator_name' => $mom->creator->name ?? 'N/A'
            ];
        }));
    }
}
