<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionItem;
use App\Models\Mom;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminCalendarController extends Controller
{
    /**
     * Display admin calendar with all action items from approved MOMs
     */
    public function index()
    {
        // Ambil semua action items dari MoM yang sudah approved
        $actionItems = ActionItem::with(['mom' => function($query) {
                            $query->where('status_id', 2); // Only approved MOMs
                        }])
                        ->whereHas('mom', function($query) {
                            $query->where('status_id', 2);
                        })
                        ->orderBy('due', 'asc')
                        ->get();

        // Format data untuk calendar
        $events = [];
        foreach ($actionItems as $item) {
            $dateKey = Carbon::parse($item->due)->format('Y-m-d');

            if (!isset($events[$dateKey])) {
                $events[$dateKey] = [];
            }

            $events[$dateKey][] = [
                'momTitle' => $item->mom->title ?? 'N/A',
                'task' => $item->item,
                'deadline' => $item->due->format('Y-m-d'),
                'createdDate' => $item->created_at->format('Y-m-d'),
                'status' => $item->status,
                'mom_id' => $item->mom_id,
                'action_id' => $item->action_id
            ];
        }

        return view('admin.calendars', compact('events'));
    }

    /**
     * Get events for specific month (API endpoint)
     */
    public function getEvents(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $actionItems = ActionItem::with(['mom' => function($query) {
                            $query->where('status_id', 2);
                        }])
                        ->whereHas('mom', function($query) {
                            $query->where('status_id', 2);
                        })
                        ->whereBetween('due', [$startDate, $endDate])
                        ->orderBy('due', 'asc')
                        ->get();

        $events = [];
        foreach ($actionItems as $item) {
            $dateKey = $item->due->format('Y-m-d');

            if (!isset($events[$dateKey])) {
                $events[$dateKey] = [];
            }

            $events[$dateKey][] = [
                'momTitle' => $item->mom->title ?? 'N/A',
                'task' => $item->item,
                'deadline' => $item->due->format('Y-m-d'),
                'createdDate' => $item->created_at->format('Y-m-d'),
                'status' => $item->status,
                'mom_id' => $item->mom_id,
                'action_id' => $item->action_id
            ];
        }

        return response()->json($events);
    }
}
