<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use App\Models\Mom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua action items dengan relasi mom
        $actionItems = ActionItem::with('mom')
                                ->whereHas('mom', function ($query) use ($userId) {
                                        $query->where('user_id', $userId); // hanya MoM milik user login
                                    })
                                 ->where('status', 'mendatang')
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

        return view('user.calendar', compact('events'));
    }

    // API untuk mendapatkan events berdasarkan bulan
    public function getEvents(Request $request)
    {
        $userId = Auth::id();
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $actionItems = ActionItem::with('mom')
            ->whereHas('mom', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'mendatang')
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
