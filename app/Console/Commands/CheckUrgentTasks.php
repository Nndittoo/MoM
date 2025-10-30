<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\NotificationController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckUrgentTasks extends Command
{
    protected $signature = 'tasks:check-urgent';
    protected $description = 'Check for action items nearing their due date and notify admins and users';

    public function handle()
    {
        $this->info('Checking for urgent tasks...');
        Log::info('CheckUrgentTasks command started');

        try {
            // Tentukan batas waktu "mendesak" (3 hari dari sekarang)
            $today = Carbon::now()->startOfDay();
            $thresholdDate = Carbon::now()->addDays(3)->endOfDay();

            $this->info("Checking tasks between {$today->toDateString()} and {$thresholdDate->toDateString()}");

            // Ambil task yang akan jatuh tempo dalam 3 hari (belum lewat deadline)
            $urgentTasks = ActionItem::with('mom.creator')
                ->where('status', 'mendatang')
                ->where('due', '>=', $today)
                ->where('due', '<=', $thresholdDate)
                ->get();

            $urgentCount = 0;
            $adminNotificationCount = 0;
            $userNotificationCount = 0;

            $this->info("Found {$urgentTasks->count()} urgent tasks");

            foreach ($urgentTasks as $task) {
                try {
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

                    // Pastikan MoM title ada
                    $momTitle = $task->mom ? $task->mom->title : 'Unknown MoM';
                    $momId = $task->mom ? $task->mom->version_id : null;

                    // === NOTIFIKASI ADMIN ===
                    // Cek apakah notifikasi admin sudah dibuat hari ini
                    $existingAdminNotification = AdminNotification::where('type', 'task_urgent')
                        ->where('related_id', $task->action_id)
                        ->whereDate('created_at', Carbon::today())
                        ->exists();

                    if (!$existingAdminNotification) {
                        AdminNotificationController::createNotification(
                            type: 'task_urgent',
                            title: 'Tugas Mendekati Deadline',
                            message: "Tugas '{$task->item}' dari MoM '{$momTitle}' akan jatuh tempo {$deadlineText}.",
                            relatedId: $task->action_id
                        );

                        $adminNotificationCount++;
                        $urgentCount++;

                        $this->info("✓ Admin notification created and FCM sent for task ID: {$task->action_id} (due in {$daysRemaining} days)");

                        Log::info("Urgent task admin notification created", [
                            'task_id' => $task->action_id,
                            'task_item' => $task->item,
                            'days_remaining' => $daysRemaining,
                            'due_date' => $task->due,
                            'mom_title' => $momTitle
                        ]);
                    }

                    // === NOTIFIKASI USER (CREATOR MoM) ===
                    // Cek apakah ada creator dan belum ada notifikasi hari ini
                    if ($task->mom && $task->mom->creator_id && $momId) {
                        $existingUserNotification = \App\Models\Notification::where('type', 'task_urgent')
                            ->where('user_id', $task->mom->creator_id)
                            ->where('mom_id', $momId)
                            ->whereDate('created_at', Carbon::today())
                            ->where('message', 'like', "%{$task->item}%") // Cek berdasarkan task item
                            ->exists();

                        if (!$existingUserNotification) {
                            NotificationController::createNotification(
                                userId: $task->mom->creator_id,
                                momId: $momId,
                                type: 'task_urgent',
                                title: 'Tugas Mendekati Deadline',
                                message: "Tugas '{$task->item}' dari MoM '{$momTitle}' akan jatuh tempo {$deadlineText}."
                            );

                            $userNotificationCount++;
                            $this->info("✓ User notification created for creator ID: {$task->mom->creator_id}");

                            Log::info("Urgent task user notification created", [
                                'task_id' => $task->action_id,
                                'user_id' => $task->mom->creator_id,
                                'task_item' => $task->item,
                                'days_remaining' => $daysRemaining
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    $this->error("Error processing task ID {$task->action_id}: " . $e->getMessage());
                    Log::error("Error processing urgent task", [
                        'task_id' => $task->action_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($adminNotificationCount > 0 || $userNotificationCount > 0) {
                $this->info("✓ Total {$adminNotificationCount} urgent task admin notifications created with FCM.");
                $this->info("✓ Total {$userNotificationCount} urgent task user notifications created.");
            } else {
                $this->info('No new urgent task notifications needed.');
            }

            Log::info('CheckUrgentTasks command completed', [
                'urgent_tasks_found' => $urgentTasks->count(),
                'admin_notifications_created' => $adminNotificationCount,
                'user_notifications_created' => $userNotificationCount
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Command failed: ' . $e->getMessage());
            Log::error('CheckUrgentTasks command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
