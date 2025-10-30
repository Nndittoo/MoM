<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActionItem;
use App\Models\AdminNotification;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\NotificationController;
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
            $overdueTasks = ActionItem::with('mom.creator')
                ->where('status', 'mendatang')
                ->where('due', '<', Carbon::now()->startOfDay())
                ->get();

            $overdueCount = 0;
            $adminNotificationCount = 0;
            $userNotificationCount = 0;

            foreach ($overdueTasks as $task) {
                try {
                    // Update status menjadi terlambat
                    $task->update(['status' => 'terlambat']);
                    $overdueCount++;

                    $this->info("Task ID {$task->action_id} status updated to 'terlambat'");

                    // Hitung berapa hari terlambat
                    $daysOverdue = Carbon::now()->startOfDay()->diffInDays($task->due);
                    $overdueText = $daysOverdue == 1 ? "1 hari" : "{$daysOverdue} hari";

                    // Pastikan MoM title ada
                    $momTitle = $task->mom ? $task->mom->title : 'Unknown MoM';
                    $momId = $task->mom ? $task->mom->version_id : null;

                    // === NOTIFIKASI ADMIN ===
                    // Cek apakah notifikasi admin sudah dibuat hari ini
                    $existingAdminNotification = AdminNotification::where('type', 'task_overdue')
                        ->where('related_id', $task->action_id)
                        ->whereDate('created_at', Carbon::today())
                        ->exists();

                    if (!$existingAdminNotification) {
                        AdminNotificationController::createNotification(
                            type: 'task_overdue',
                            title: 'Tugas Terlambat',
                            message: "Tugas '{$task->item}' dari MoM '{$momTitle}' sudah terlambat {$overdueText}.",
                            relatedId: $task->action_id
                        );

                        $adminNotificationCount++;
                        $this->info("Admin notification created and FCM sent for task ID: {$task->action_id}");

                        Log::info("Overdue task admin notification created", [
                            'task_id' => $task->action_id,
                            'task_item' => $task->item,
                            'days_overdue' => $daysOverdue,
                            'mom_title' => $momTitle
                        ]);
                    }

                    // === NOTIFIKASI USER (CREATOR MoM) ===
                    // Cek apakah ada creator dan belum ada notifikasi hari ini
                    if ($task->mom && $task->mom->creator_id && $momId) {
                        $existingUserNotification = \App\Models\Notification::where('type', 'task_overdue')
                            ->where('user_id', $task->mom->creator_id)
                            ->where('mom_id', $momId)
                            ->whereDate('created_at', Carbon::today())
                            ->where('message', 'like', "%{$task->item}%") // Cek berdasarkan task item
                            ->exists();

                        if (!$existingUserNotification) {
                            NotificationController::createNotification(
                                userId: $task->mom->creator_id,
                                momId: $momId,
                                type: 'task_overdue',
                                title: 'Tugas Anda Terlambat',
                                message: "Tugas '{$task->item}' dari MoM '{$momTitle}' sudah terlambat {$overdueText}."
                            );

                            $userNotificationCount++;
                            $this->info("User notification created for creator ID: {$task->mom->creator_id}");

                            Log::info("Overdue task user notification created", [
                                'task_id' => $task->action_id,
                                'user_id' => $task->mom->creator_id,
                                'task_item' => $task->item,
                                'days_overdue' => $daysOverdue
                            ]);
                        }
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
                $this->info("✓ Total {$adminNotificationCount} new admin notifications created with FCM.");
                $this->info("✓ Total {$userNotificationCount} new user notifications created.");
            } else {
                $this->info('No overdue tasks found.');
            }

            Log::info('CheckOverdueTasks command completed', [
                'overdue_count' => $overdueCount,
                'admin_notification_count' => $adminNotificationCount,
                'user_notification_count' => $userNotificationCount
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
