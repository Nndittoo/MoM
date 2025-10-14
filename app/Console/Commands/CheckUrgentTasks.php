<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Http\Controllers\Admin\AdminNotificationController;
use Carbon\Carbon;

class CheckUrgentTasks extends Command
{
    protected $signature = 'tasks:check-urgent';
    protected $description = 'Check for action items nearing their due date and notify admins';

    public function handle()
    {
        $this->info('Checking for urgent tasks...');

        // Tentukan batas waktu "mendesak" (3 hari dari sekarang)
        $today = Carbon::now()->startOfDay();
        $thresholdDate = Carbon::now()->addDays(3)->endOfDay();

        // Ambil task yang akan jatuh tempo dalam 3 hari (belum lewat deadline)
        $urgentTasks = ActionItem::with('mom')
            ->where('status', 'mendatang')
            ->where('due', '>=', $today) // Belum lewat deadline
            ->where('due', '<=', $thresholdDate) // Dalam 3 hari ke depan
            ->get();

        $urgentCount = 0;

        foreach ($urgentTasks as $task) {
            // Cek apakah notifikasi untuk tugas ini sudah pernah dibuat hari ini
            $existingNotification = AdminNotification::where('type', 'task_urgent')
                ->where('related_id', $task->action_id)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existingNotification) {
                // Hitung sisa hari dengan pembulatan ke atas (ceil)
                $dueDate = Carbon::parse($task->due)->startOfDay();
                $daysRemaining = (int) ceil($today->diffInDays($dueDate, false));

                // Format text deadline
                $deadlineText = match(true) {
                    $daysRemaining == 0 => "hari ini",
                    $daysRemaining == 1 => "besok (1 hari lagi)",
                    $daysRemaining > 1  => "dalam {$daysRemaining} hari",
                    default => "segera"
                };

                AdminNotificationController::createNotification(
                    type: 'task_urgent',
                    title: 'Tugas Mendekati Deadline',
                    message: "Tugas '{$task->item}' dari MoM '{$task->mom->title}' akan jatuh tempo {$deadlineText}.",
                    relatedId: $task->action_id
                );

                $urgentCount++;
                $this->info("Notification created for task ID: {$task->action_id}");
            }
        }

        if ($urgentCount > 0) {
            $this->info("Total {$urgentCount} urgent task notifications created.");
        } else {
            $this->info('No urgent tasks found or all notifications already sent.');
        }

        return 0;
    }
}
