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

        // Tentukan batas waktu "mendesak" (misal: 3 hari dari sekarang)
        $thresholdDate = Carbon::now()->addDays(3);

        $urgentTasks = ActionItem::with('mom')
            ->where('status', 'mendatang') // Hanya periksa tugas yang masih pending
            ->where('due', '<=', $thresholdDate)
            ->where('due', '>', Carbon::now()) // Pastikan belum lewat deadline
            ->get();

        foreach ($urgentTasks as $task) {
            // Cek apakah notifikasi untuk tugas ini sudah pernah dibuat
            $existingNotification = AdminNotification::where('type', 'task_urgent')
                                                      ->where('related_id', $task->id)
                                                      ->exists();

            if (!$existingNotification) {
                $daysRemaining = Carbon::now()->diffInDays($task->due, false); // false agar tidak absolut
                $deadlineText = $daysRemaining <= 0 ? "hari ini" : "dalam {$daysRemaining} hari";

                AdminNotificationController::createNotification(
                    type: 'task_urgent',
                    title: 'Tugas Mendekati Deadline',
                    message: "Tugas '{$task->item}' dari MoM '{$task->mom->title}' akan jatuh tempo {$deadlineText}.",
                    relatedId: $task->id
                );
                $this->info("Notification created for task ID: {$task->id}");
            }
        }

        $this->info('Urgent task check completed.');
        return 0;
    }
}
