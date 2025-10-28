<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Http\Controllers\Admin\AdminNotificationController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check for overdue action items and update their status';

    public function handle()
    {
        $this->info('Checking for overdue tasks...');
        Log::info('CheckOverdueTasks command started');

        try {
            // Ambil semua task yang sudah lewat deadline dan masih berstatus 'mendatang'
            $overdueTasks = ActionItem::with('mom')
                ->where('status', 'mendatang')
                ->where('due', '<', Carbon::now()->startOfDay())
                ->get();

            $overdueCount = 0;
            $notificationCount = 0;

            foreach ($overdueTasks as $task) {
                try {
                    // Update status menjadi terlambat
                    $task->update(['status' => 'terlambat']);
                    $overdueCount++;

                    $this->info("Task ID {$task->action_id} status updated to 'terlambat'");

                    // Cek apakah notifikasi untuk tugas ini sudah pernah dibuat hari ini
                    $existingNotification = AdminNotification::where('type', 'task_overdue')
                        ->where('related_id', $task->action_id)
                        ->whereDate('created_at', Carbon::today())
                        ->exists();

                    if (!$existingNotification) {
                        $daysOverdue = Carbon::now()->startOfDay()->diffInDays($task->due);
                        $overdueText = $daysOverdue == 1 ? "1 hari" : "{$daysOverdue} hari";

                        // Pastikan MoM title ada
                        $momTitle = $task->mom ? $task->mom->title : 'Unknown MoM';

                        // Buat notifikasi (FCM otomatis trigger dari model event)
                        AdminNotificationController::createNotification(
                            type: 'task_overdue',
                            title: 'Tugas Terlambat',
                            message: "Tugas '{$task->item}' dari MoM '{$momTitle}' sudah terlambat {$overdueText}.",
                            relatedId: $task->action_id
                        );

                        $notificationCount++;
                        $this->info("Notification created and FCM sent for task ID: {$task->action_id}");

                        Log::info("Overdue task notification created", [
                            'task_id' => $task->action_id,
                            'task_item' => $task->item,
                            'days_overdue' => $daysOverdue,
                            'mom_title' => $momTitle
                        ]);
                    } else {
                        $this->info("Notification already exists for task ID: {$task->action_id}");
                    }

                } catch (\Exception $e) {
                    $this->error("Error processing task ID {$task->action_id}: " . $e->getMessage());
                    Log::error("Error processing overdue task", [
                        'task_id' => $task->action_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($overdueCount > 0) {
                $this->info("✓ Total {$overdueCount} tasks updated to 'terlambat' status.");
                $this->info("✓ Total {$notificationCount} new notifications created with FCM.");
            } else {
                $this->info('No overdue tasks found.');
            }

            Log::info('CheckOverdueTasks command completed', [
                'overdue_count' => $overdueCount,
                'notification_count' => $notificationCount
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Command failed: ' . $e->getMessage());
            Log::error('CheckOverdueTasks command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
