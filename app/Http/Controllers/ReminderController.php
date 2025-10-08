<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $userId = Auth::id();

        //Urgent tasks (deadline < 24 jam)
        $urgentTasks = ActionItem::with('mom')
            ->whereHas('mom', function ($query) use ($userId) {
                $query->where('creator_id', $userId);
            })
            ->where('status', 'mendatang')
            ->where('due', '>', $now)
            ->where('due', '<=', $now->copy()->addHours(24))
            ->orderBy('due', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $dueDate = Carbon::parse($task->due);
                $diffInHours = $now->diffInHours($dueDate, false);

                return [
                    'action_id' => $task->action_id,
                    'task' => $task->item,
                    'mom_title' => $task->mom->title ?? 'N/A',
                    'mom_id' => $task->mom_id,
                    'deadline' => $dueDate,
                    'deadline_formatted' => $dueDate->translatedFormat('l, d F Y'),
                    'hours_remaining' => ceil($diffInHours),
                    'badge' => $this->getTimeBadge(ceil($diffInHours)),
                    'border_color' => 'border-red-500',
                    'bg_color' => 'bg-red-100',
                    'text_color' => 'text-red-600',
                    'dark_bg' => 'dark:bg-red-500/20',
                    'dark_text' => 'dark:text-red-400',
                ];
            });

        //Weekly tasks (1–7 hari ke depan)
        $weeklyTasks = ActionItem::with('mom')
            ->whereHas('mom', function ($query) use ($userId) {
                $query->where('creator_id', $userId);
            })
            ->where('status', 'mendatang')
            ->where('due', '>', $now->copy()->addHours(24))
            ->where('due', '<=', $now->copy()->addDays(7))
            ->orderBy('due', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $dueDate = Carbon::parse($task->due);
                $diffInDays = ceil($now->diffInDays($dueDate, false));

                return [
                    'action_id' => $task->action_id,
                    'task' => $task->item,
                    'mom_title' => $task->mom->title ?? 'N/A',
                    'mom_id' => $task->mom_id,
                    'deadline' => $dueDate,
                    'deadline_formatted' => $dueDate->translatedFormat('l, d F Y'),
                    'days_remaining' => $diffInDays,
                    'badge' => $diffInDays . ' Hari Lagi',
                    'border_color' => 'border-yellow-500',
                    'bg_color' => 'bg-yellow-100',
                    'text_color' => 'text-yellow-600',
                    'dark_bg' => 'dark:bg-yellow-500/20',
                    'dark_text' => 'dark:text-yellow-400',
                ];
            });

        $stats = [
            'urgent_count' => $urgentTasks->count(),
            'weekly_count' => $weeklyTasks->count(),
            'total_count' => $urgentTasks->count() + $weeklyTasks->count(),
        ];

        return view('user.reminder', compact('urgentTasks', 'weeklyTasks', 'stats'));
    }

    private function getTimeBadge($hours)
    {
        if ($hours < 1) return 'Segera';
        if ($hours <= 3) return 'Beberapa Jam Lagi';
        if ($hours <= 12) return 'Hari Ini';
        return 'Besok';
    }

    public function complete($id)
    {
        $task = ActionItem::findOrFail($id);
        $task->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Task berhasil diselesaikan!');
    }

    public static function getReminderCount($userId = null)
    {
        $now = Carbon::now();

        return ActionItem::where('status', 'mendatang')
            ->where('due', '>', $now)
            ->where('due', '<=', $now->copy()->addDays(7))
            ->whereHas('mom', function ($query) use ($userId) {
                if ($userId) {
                    $query->where('creator_id', $userId);
                }
            })
            ->count();
    }
}
