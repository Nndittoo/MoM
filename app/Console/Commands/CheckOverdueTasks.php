<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Http\Controllers\Admin\AdminNotificationController;
use Carbon\Carbon;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check for overdue action items and update their status';

    public function handle()
    {
        $this->info('Checking for overdue tasks...');

        // Ambil semua task yang sudah lewat deadline dan masih berstatus 'mendatang'
        $overdueTasks = ActionItem::with('mom')
            ->where('status', 'mendatang')
            ->where('due', '<', Carbon::now()->startOfDay())
            ->get();

        $overdueCount = 0;

        foreach ($overdueTasks as $task) {
            // Update status menjadi terlambat
            $task->update(['status' => 'terlambat']);
            $overdueCount++;

            // Cek apakah notifikasi untuk tugas ini sudah pernah dibuat
            $existingNotification = AdminNotification::where('type', 'task_overdue')
                ->where('related_id', $task->action_id)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existingNotification) {
                $daysOverdue = Carbon::now()->startOfDay()->diffInDays($task->due);
                $overdueText = $daysOverdue == 1 ? "1 hari" : "{$daysOverdue} hari";

                AdminNotificationController::createNotification(
                    type: 'task_overdue',
                    title: 'Tugas Terlambat',
                    message: "Tugas '{$task->item}' dari MoM '{$task->mom->title}' sudah terlambat {$overdueText}.",
                    relatedId: $task->action_id
                );

                $this->info("Status updated and notification created for task ID: {$task->action_id}");
            }
        }

        if ($overdueCount > 0) {
            $this->info("Total {$overdueCount} tasks updated to 'terlambat' status.");
        } else {
            $this->info('No overdue tasks found.');
        }

        return 0;
    }
}
