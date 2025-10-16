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
        // Hanya sync jika MoM sudah disetujui (status_id = 2)
        if ($actionItem->mom && $actionItem->mom->status_id == 2 && !$actionItem->google_event_id) {
            $this->syncToGoogleCalendar($actionItem, 'created');
        }
    }

    /**
     * Handle the ActionItem "updated" event.
     * Update event di Google Calendar saat action item diupdate
     */
    public function updated(ActionItem $actionItem)
    {
        // Jika sudah ada google_event_id, update event-nya
        if ($actionItem->google_event_id && $actionItem->mom && $actionItem->mom->status_id == 2) {
            $this->syncToGoogleCalendar($actionItem, 'updated');
        }
        // Jika belum ada google_event_id tapi MoM sudah disetujui, buat event baru
        elseif (!$actionItem->google_event_id && $actionItem->mom && $actionItem->mom->status_id == 2) {
            $this->syncToGoogleCalendar($actionItem, 'created');
        }
    }

    /**
     * Handle the ActionItem "deleted" event.
     * Hapus event dari Google Calendar saat action item dihapus
     */
    public function deleted(ActionItem $actionItem)
    {
        if ($actionItem->google_event_id) {
            Log::info("Attempting to delete Google Calendar event", [
                'action_item_id' => $actionItem->action_id,
                'google_event_id' => $actionItem->google_event_id,
                'item' => $actionItem->item
            ]);

            $this->syncToGoogleCalendar($actionItem, 'deleted');
        } else {
            Log::info("No Google Calendar event to delete", [
                'action_item_id' => $actionItem->action_id,
                'item' => $actionItem->item
            ]);
        }
    }

    /**
     * Sync action item ke Google Calendar
     */
    private function syncToGoogleCalendar(ActionItem $actionItem, string $action)
    {
        try {
            // Ambil user creator dari MoM
            $creator = $actionItem->mom->creator;

            // Cek apakah user memiliki token Google Calendar
            if (!$creator || !$creator->google_access_token) {
                Log::info("Skipping Google Calendar sync: User tidak memiliki token", [
                    'action_item_id' => $actionItem->action_id,
                    'action' => $action
                ]);
                return;
            }

            switch ($action) {
                case 'created':
                    $eventId = $this->googleCalendar->createEvent($actionItem, $creator);

                    // Update action item dengan google_event_id tanpa trigger observer lagi
                    $actionItem->withoutEvents(function () use ($actionItem, $eventId) {
                        $actionItem->google_event_id = $eventId;
                        $actionItem->save();
                    });

                    Log::info("Google Calendar event created automatically", [
                        'action_item_id' => $actionItem->action_id,
                        'event_id' => $eventId
                    ]);
                    break;

                case 'updated':
                    $this->googleCalendar->updateEvent(
                        $actionItem->google_event_id,
                        $actionItem,
                        $creator
                    );

                    Log::info("Google Calendar event updated automatically", [
                        'action_item_id' => $actionItem->action_id,
                        'event_id' => $actionItem->google_event_id
                    ]);
                    break;

                case 'deleted':
                    $this->googleCalendar->deleteEvent(
                        $actionItem->google_event_id,
                        $creator
                    );

                    Log::info("Google Calendar event deleted automatically", [
                        'action_item_id' => $actionItem->action_id,
                        'event_id' => $actionItem->google_event_id
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to sync action item to Google Calendar", [
                'action_item_id' => $actionItem->action_id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            // Jangan throw exception agar proses utama tidak terganggu
        }
    }
}
