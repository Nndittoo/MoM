<?php

namespace App\Observers;

use App\Models\ActionItem;
use App\Services\GoogleCalendarService;

class ActionItemObserver
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Handle ActionItem "created" event
     */
    public function created(ActionItem $actionItem)
    {
        // Dapatkan user dari MoM creator
        $user = $actionItem->mom->creator;

        // Jika user memiliki token Google Calendar, auto-sync
        if ($user && $user->google_access_token && $actionItem->status === 'mendatang') {
            try {
                $eventId = $this->googleCalendar->createEvent($actionItem, $user);
                $actionItem->google_event_id = $eventId;
                $actionItem->saveQuietly(); // Save tanpa trigger observer lagi
            } catch (\Exception $e) {
                \Log::error("Error auto-syncing action item to Google Calendar: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle ActionItem "updated" event
     */
    public function updated(ActionItem $actionItem)
    {
        $user = $actionItem->mom->creator;

        if ($user && $user->google_access_token && $actionItem->google_event_id) {
            try {
                // Update event di Google Calendar
                $this->googleCalendar->updateEvent(
                    $actionItem->google_event_id,
                    $actionItem,
                    $user
                );
            } catch (\Exception $e) {
                \Log::error("Error updating Google Calendar event: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle ActionItem "deleted" event
     */
    public function deleted(ActionItem $actionItem)
    {
        $user = $actionItem->mom->creator;

        if ($user && $user->google_access_token && $actionItem->google_event_id) {
            try {
                // Hapus event dari Google Calendar
                $this->googleCalendar->deleteEvent($actionItem->google_event_id, $user);
            } catch (\Exception $e) {
                \Log::error("Error deleting Google Calendar event: " . $e->getMessage());
            }
        }
    }
}
