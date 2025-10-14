<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActionItem;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(config('google-calendar.application_name'));
        $this->client->setClientId(config('google-calendar.client_id'));
        $this->client->setClientSecret(config('google-calendar.client_secret'));
        $this->client->setRedirectUri(config('google-calendar.redirect_uri'));
        $this->client->setScopes(config('google-calendar.scopes'));
        $this->client->setAccessType(config('google-calendar.access_type'));
        $this->client->setApprovalPrompt(config('google-calendar.approval_prompt'));
    }

    public function getAuthUrl($state = null)
    {
        if ($state) {
            $this->client->setState($state);
        }
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);

        // Refresh token jika expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            if ($refreshToken) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                return $newToken;
            }
        }

        return null;
    }

    public function getCalendarService()
    {
        return new Google_Service_Calendar($this->client);
    }

    public function createEvent(ActionItem $actionItem, User $user)
    {
        // Set access token
        if ($user->google_access_token) {
            $accessToken = json_decode($user->google_access_token, true);
            $newToken = $this->setAccessToken($accessToken);

            // Update token jika di-refresh
            if ($newToken) {
                $user->google_access_token = json_encode($newToken);
                $user->google_token_expires_at = Carbon::now()->addSeconds($newToken['expires_in']);
                $user->save();
            }
        } else {
            throw new \Exception('User tidak memiliki token Google Calendar');
        }

        $service = $this->getCalendarService();

        // Format event untuk Google Calendar
        $event = new Google_Service_Calendar_Event([
            'summary' => $actionItem->item,
            'description' => "MoM: {$actionItem->mom->title}\nCreated: {$actionItem->created_at->format('d/m/Y')}",
            'start' => new Google_Service_Calendar_EventDateTime([
                'date' => $actionItem->due->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ]),
            'end' => new Google_Service_Calendar_EventDateTime([
                'date' => $actionItem->due->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ]),
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60], // 1 hari sebelum
                    ['method' => 'popup', 'minutes' => 60], // 1 jam sebelum
                ],
            ],
            'colorId' => '11', // Red color untuk highlight
        ]);

        $calendarId = 'primary';

        try {
            $createdEvent = $service->events->insert($calendarId, $event);

            // Log success
            \Log::info("Google Calendar Event Created", [
                'event_id' => $createdEvent->getId(),
                'summary' => $createdEvent->getSummary(),
                'start' => $createdEvent->getStart()->getDate(),
                'html_link' => $createdEvent->getHtmlLink(),
                'action_item_id' => $actionItem->action_id,
            ]);

            return $createdEvent->getId();
        } catch (\Exception $e) {
            \Log::error("Failed to create Google Calendar event", [
                'error' => $e->getMessage(),
                'action_item_id' => $actionItem->action_id,
            ]);
            throw $e;
        }
    }

    public function updateEvent($eventId, ActionItem $actionItem, User $user)
    {
        if ($user->google_access_token) {
            $accessToken = json_decode($user->google_access_token, true);
            $newToken = $this->setAccessToken($accessToken);

            if ($newToken) {
                $user->google_access_token = json_encode($newToken);
                $user->google_token_expires_at = Carbon::now()->addSeconds($newToken['expires_in']);
                $user->save();
            }
        } else {
            throw new \Exception('User tidak memiliki token Google Calendar');
        }

        $service = $this->getCalendarService();

        $event = new Google_Service_Calendar_Event([
            'summary' => $actionItem->item,
            'description' => "MoM: {$actionItem->mom->title}\nCreated: {$actionItem->created_at->format('d/m/Y')}",
            'start' => new Google_Service_Calendar_EventDateTime([
                'date' => $actionItem->due->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ]),
            'end' => new Google_Service_Calendar_EventDateTime([
                'date' => $actionItem->due->format('Y-m-d'),
                'timeZone' => 'Asia/Jakarta',
            ]),
        ]);

        $calendarId = 'primary';
        return $service->events->update($calendarId, $eventId, $event);
    }

    public function deleteEvent($eventId, User $user)
    {
        if ($user->google_access_token) {
            $accessToken = json_decode($user->google_access_token, true);
            $this->setAccessToken($accessToken);
        } else {
            throw new \Exception('User tidak memiliki token Google Calendar');
        }

        $service = $this->getCalendarService();
        $calendarId = 'primary';

        return $service->events->delete($calendarId, $eventId);
    }

    public function syncAllEvents(User $user)
    {
        $actionItems = ActionItem::with('mom')
            ->whereHas('mom', function ($query) use ($user) {
                $query->where('creator_id', $user->id);
            })
            ->where('status', 'mendatang')
            ->whereNull('google_event_id')
            ->get();

        $synced = 0;
        foreach ($actionItems as $item) {
            try {
                $eventId = $this->createEvent($item, $user);
                $item->google_event_id = $eventId;
                $item->save();
                $synced++;
            } catch (\Exception $e) {
                \Log::error("Error syncing action item {$item->action_id}: " . $e->getMessage());
            }
        }

        return $synced;
    }

    public function syncAllApprovedEvents(User $user)
    {
        // Untuk admin: sync semua action items dari MoM yang sudah approved
        $actionItems = ActionItem::with('mom')
            ->whereHas('mom', function ($query) {
                $query->where('status_id', 2); // status_id 2 = approved
            })
            ->where('status', 'mendatang')
            ->whereNull('google_event_id')
            ->get();

        $synced = 0;
        foreach ($actionItems as $item) {
            try {
                $eventId = $this->createEvent($item, $user);
                $item->google_event_id = $eventId;
                $item->save();
                $synced++;
            } catch (\Exception $e) {
                \Log::error("Error syncing action item {$item->action_id}: " . $e->getMessage());
            }
        }

        return $synced;
    }
}
