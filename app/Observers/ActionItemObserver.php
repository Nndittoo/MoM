<?php

namespace App\Observers;

use App\Models\ActionItem;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

class ActionItemObserver
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Handle the ActionItem "created" event.
     * Sinkronisasi otomatis saat action item baru dibuat
     */
    public function created(ActionItem $actionItem)
    {
        // Load relasi mom jika belum
        if (!$actionItem->relationLoaded('mom')) {
            $actionItem->load('mom');
        }

        // Validasi: pastikan mom exists
        if (!$actionItem->mom) {
            Log::warning("Action item created without MoM", [
                'action_id' => $actionItem->action_id
            ]);
            return;
        }

        // Hanya sync jika MoM sudah disetujui (status_id = 2)
        if ($actionItem->mom->status_id != 2) {
            Log::info("Action item not synced: MoM not approved yet", [
                'action_id' => $actionItem->action_id,
                'mom_id' => $actionItem->mom->version_id,
                'status_id' => $actionItem->mom->status_id
            ]);
            return;
        }

        // Skip jika sudah punya google_event_id (avoid double sync)
        if ($actionItem->google_event_id) {
            return;
        }

        $this->syncToAllConnectedUsers($actionItem, 'created');
    }

    /**
     * Handle the ActionItem "updated" event.
     * Update event di Google Calendar saat action item diupdate
     */
    public function updated(ActionItem $actionItem)
    {
        // Skip jika update hanya untuk set google_event_id
        if ($actionItem->wasChanged('google_event_id') && count($actionItem->getChanges()) === 1) {
            return;
        }

        // Load relasi mom jika belum
        if (!$actionItem->relationLoaded('mom')) {
            $actionItem->load('mom');
        }

        if (!$actionItem->mom) {
            return;
        }

        // Jika sudah ada google_event_id dan MoM masih disetujui, update event
        if ($actionItem->google_event_id && $actionItem->mom->status_id == 2) {
            $this->syncToAllConnectedUsers($actionItem, 'updated');
        }
        // Jika belum ada google_event_id tapi MoM sudah disetujui, buat event baru
        elseif (!$actionItem->google_event_id && $actionItem->mom->status_id == 2) {
            $this->syncToAllConnectedUsers($actionItem, 'created');
        }
    }

    /**
     * Handle the ActionItem "deleted" event.
     * Hapus event dari Google Calendar saat action item dihapus
     */
    public function deleted(ActionItem $actionItem)
    {
        if (!$actionItem->google_event_id) {
            Log::info("No Google Calendar event to delete", [
                'action_item_id' => $actionItem->action_id,
                'item' => $actionItem->item
            ]);
            return;
        }

        Log::info("Attempting to delete Google Calendar event", [
            'action_item_id' => $actionItem->action_id,
            'google_event_id' => $actionItem->google_event_id,
            'item' => $actionItem->item
        ]);

        $this->syncToAllConnectedUsers($actionItem, 'deleted');
    }

    /**
     * Sync action item ke semua user yang connected ke Google Calendar
     * (Creator MoM + Semua Admin yang connected)
     */
    private function syncToAllConnectedUsers(ActionItem $actionItem, string $action)
    {
        $usersToSync = collect();
        $synced = 0;
        $failed = 0;

        // 1. Tambahkan creator MoM jika connected
        $creator = $actionItem->mom->creator;
        if ($creator && $creator->google_access_token) {
            $usersToSync->push($creator);
        }

        // 2. Tambahkan semua admin yang connected
        $admins = \App\Models\User::where('role', 'admin')
            ->whereNotNull('google_access_token')
            ->get();
        
        $usersToSync = $usersToSync->merge($admins)->unique('id');

        // Jika tidak ada user yang connected, skip
        if ($usersToSync->isEmpty()) {
            Log::info("No connected users to sync", [
                'action_id' => $actionItem->action_id,
                'action' => $action,
                'creator_connected' => $creator ? (bool)$creator->google_access_token : false,
                'admins_connected' => $admins->count()
            ]);
            return;
        }

        // Sync ke semua user yang connected
        foreach ($usersToSync as $user) {
            try {
                switch ($action) {
                    case 'created':
                        $eventId = $this->googleCalendar->createEvent($actionItem, $user);

                        // Simpan google_event_id hanya sekali (dari user pertama)
                        if ($synced === 0) {
                            $actionItem->withoutEvents(function () use ($actionItem, $eventId) {
                                $actionItem->google_event_id = $eventId;
                                $actionItem->save();
                            });
                        }

                        Log::info("Google Calendar event created", [
                            'action_id' => $actionItem->action_id,
                            'event_id' => $eventId,
                            'user_id' => $user->id,
                            'user_role' => $user->role
                        ]);
                        $synced++;
                        break;

                    case 'updated':
                        $this->googleCalendar->updateEvent(
                            $actionItem->google_event_id,
                            $actionItem,
                            $user
                        );

                        Log::info("Google Calendar event updated", [
                            'action_id' => $actionItem->action_id,
                            'event_id' => $actionItem->google_event_id,
                            'user_id' => $user->id,
                            'user_role' => $user->role
                        ]);
                        $synced++;
                        break;

                    case 'deleted':
                        $this->googleCalendar->deleteEvent(
                            $actionItem->google_event_id,
                            $user
                        );

                        Log::info("Google Calendar event deleted", [
                            'action_id' => $actionItem->action_id,
                            'event_id' => $actionItem->google_event_id,
                            'user_id' => $user->id,
                            'user_role' => $user->role
                        ]);
                        $synced++;
                        break;
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to sync action item to user's calendar", [
                    'action_id' => $actionItem->action_id,
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Sync action completed", [
            'action_id' => $actionItem->action_id,
            'action' => $action,
            'users_attempted' => $usersToSync->count(),
            'synced' => $synced,
            'failed' => $failed
        ]);
    }
}