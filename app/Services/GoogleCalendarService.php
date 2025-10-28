<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use App\Models\ActionItem;
use App\Models\Mom;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get authorization URL
     */
    public function getAuthUrl($state = null)
    {
        if ($state) {
            $this->client->setState($state);
        }
        return $this->client->createAuthUrl();
    }

    /**
     * Authenticate with authorization code
     */
    public function authenticate($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception('Error fetching access token: ' . $token['error']);
        }
        
        return $token;
    }

    /**
     * Get authenticated Google Client for user
     */
    protected function getClient($user)
    {
        if (!$user->google_access_token) {
            throw new \Exception('User tidak memiliki access token Google Calendar');
        }

        $token = json_decode($user->google_access_token, true);
        
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($token);

        // Refresh token jika expired
        if ($client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $client->getAccessToken();
                
                // Update token di database
                $user->google_access_token = json_encode($newToken);
                $user->google_token_expires_at = Carbon::now()->addSeconds($newToken['expires_in']);
                $user->save();
            } else {
                throw new \Exception('Refresh token tidak tersedia. Silakan hubungkan ulang akun Google Calendar.');
            }
        }

        return $client;
    }

    /**
     * Create a single event in Google Calendar
     * Signature disesuaikan dengan Observer: createEvent($actionItem, $user)
     */
    public function createEvent($actionItem, $user)
    {
        try {
            $client = $this->getClient($user);
            $service = new Google_Service_Calendar($client);

            // Load mom relation jika belum
            if (!$actionItem->relationLoaded('mom')) {
                $actionItem->load('mom');
            }

            // Format tanggal ke Y-m-d
            $dueDate = Carbon::parse($actionItem->due)->format('Y-m-d');

            $event = new Google_Service_Calendar_Event([
                'summary' => $actionItem->item,
                'description' => "Dari MoM: {$actionItem->mom->title}",
                'start' => [
                    'date' => $dueDate,
                    'timeZone' => 'Asia/Jakarta',
                ],
                'end' => [
                    'date' => $dueDate,
                    'timeZone' => 'Asia/Jakarta',
                ],
            ]);

            $createdEvent = $service->events->insert('primary', $event);
            
            Log::info("Google Calendar event created", [
                'action_id' => $actionItem->action_id,
                'event_id' => $createdEvent->id,
                'user_id' => $user->id,
                'due_date' => $dueDate
            ]);
            
            return $createdEvent->id;
        } catch (\Exception $e) {
            Log::error("Failed to create Google Calendar event: " . $e->getMessage(), [
                'action_id' => $actionItem->action_id ?? null,
                'user_id' => $user->id ?? null,
                'due_date' => $actionItem->due ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Delete a single event from Google Calendar
     * Signature disesuaikan dengan Observer: deleteEvent($eventId, $user)
     */
    public function deleteEvent($eventId, $user)
    {
        try {
            $client = $this->getClient($user);
            $service = new Google_Service_Calendar($client);
            
            $service->events->delete('primary', $eventId);
            
            Log::info("Google Calendar event deleted", [
                'event_id' => $eventId,
                'user_id' => $user->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete Google Calendar event {$eventId}: " . $e->getMessage(), [
                'user_id' => $user->id ?? null
            ]);
            return false;
        }
    }

    /**
     * Delete multiple events from Google Calendar
     */
    public function deleteMultipleEvents($user, array $eventIds)
    {
        $client = $this->getClient($user);
        $service = new Google_Service_Calendar($client);
        
        $deletedCount = 0;
        
        foreach ($eventIds as $eventId) {
            try {
                $service->events->delete('primary', $eventId);
                $deletedCount++;
                
                Log::info("Deleted event from Google Calendar", [
                    'event_id' => $eventId,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to delete event {$eventId}: " . $e->getMessage());
            }
        }
        
        return $deletedCount;
    }

    /**
     * Sync all events for regular user (their own action items)
     */
    public function syncAllEvents($user)
    {
        $client = $this->getClient($user);
        $service = new Google_Service_Calendar($client);
        
        // Ambil action items untuk user ini (hanya dari MoM yang mereka buat)
        $actionItems = ActionItem::whereHas('mom', function($query) use ($user) {
            $query->where('creator_id', $user->id)
                  ->whereHas('status', function($q) {
                      $q->where('status', 'Disetujui');
                  });
        })
        ->with('mom')
        ->whereNull('google_event_id') // Hanya yang belum di-sync
        ->get();
        
        $synced = 0;
        $failed = 0;
        
        foreach ($actionItems as $actionItem) {
            try {
                // Validasi data sebelum sync
                if (!$actionItem->item || !$actionItem->due || !$actionItem->mom) {
                    Log::warning("Skipping invalid action item", [
                        'action_id' => $actionItem->action_id,
                        'item' => $actionItem->item,
                        'due' => $actionItem->due,
                        'has_mom' => (bool) $actionItem->mom
                    ]);
                    $failed++;
                    continue;
                }
                
                // Format tanggal ke Y-m-d
                $dueDate = Carbon::parse($actionItem->due)->format('Y-m-d');
                
                // Validasi tanggal tidak di masa lalu
                if (Carbon::parse($dueDate)->isPast()) {
                    Log::warning("Skipping past date action item", [
                        'action_id' => $actionItem->action_id,
                        'due_date' => $dueDate
                    ]);
                    $failed++;
                    continue;
                }
                
                Log::info("Attempting to sync action item", [
                    'action_id' => $actionItem->action_id,
                    'item' => $actionItem->item,
                    'due_date' => $dueDate,
                    'mom_title' => $actionItem->mom->title
                ]);
                
                $event = new Google_Service_Calendar_Event([
                    'summary' => $actionItem->item,
                    'description' => "Dari MoM: {$actionItem->mom->title}",
                    'start' => [
                        'date' => $dueDate,
                        'timeZone' => 'Asia/Jakarta',
                    ],
                    'end' => [
                        'date' => $dueDate,
                        'timeZone' => 'Asia/Jakarta',
                    ],
                ]);
                
                $createdEvent = $service->events->insert('primary', $event);
                
                // Simpan Google Event ID
                $actionItem->update(['google_event_id' => $createdEvent->id]);
                
                Log::info("Successfully synced action item", [
                    'action_id' => $actionItem->action_id,
                    'event_id' => $createdEvent->id
                ]);
                
                $synced++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to sync action item {$actionItem->action_id}: " . $e->getMessage(), [
                    'action_id' => $actionItem->action_id,
                    'item' => $actionItem->item ?? 'N/A',
                    'due' => $actionItem->due ?? 'N/A',
                    'exception' => get_class($e)
                ]);
            }
        }
        
        Log::info("Sync completed", [
            'user_id' => $user->id,
            'synced' => $synced,
            'failed' => $failed,
            'total' => $actionItems->count()
        ]);
        
        return $synced;
    }

    /**
     * Sync all approved events (untuk admin)
     */
    public function syncAllApprovedEvents($user)
    {
        $client = $this->getClient($user);
        $service = new Google_Service_Calendar($client);
        
        // Ambil action items dari MoM yang approved
        $actionItems = ActionItem::whereHas('mom', function($query) {
            $query->whereHas('status', function($q) {
                $q->where('status', 'Disetujui');
            });
        })
        ->with('mom')
        ->whereNull('google_event_id') // Hanya yang belum di-sync
        ->get();
        
        $synced = 0;
        $failed = 0;
        
        foreach ($actionItems as $actionItem) {
            try {
                // Validasi data sebelum sync
                if (!$actionItem->item || !$actionItem->due || !$actionItem->mom) {
                    Log::warning("Admin sync: Skipping invalid action item", [
                        'action_id' => $actionItem->action_id,
                        'item' => $actionItem->item,
                        'due' => $actionItem->due,
                        'has_mom' => (bool) $actionItem->mom
                    ]);
                    $failed++;
                    continue;
                }
                
                // Format tanggal ke Y-m-d
                $dueDate = Carbon::parse($actionItem->due)->format('Y-m-d');
                
                Log::info("Admin attempting to sync action item", [
                    'action_id' => $actionItem->action_id,
                    'item' => $actionItem->item,
                    'due_date' => $dueDate,
                    'mom_title' => $actionItem->mom->title
                ]);
                
                $event = new Google_Service_Calendar_Event([
                    'summary' => $actionItem->item,
                    'description' => "Dari MoM: {$actionItem->mom->title}",
                    'start' => [
                        'date' => $dueDate,
                        'timeZone' => 'Asia/Jakarta',
                    ],
                    'end' => [
                        'date' => $dueDate,
                        'timeZone' => 'Asia/Jakarta',
                    ],
                ]);
                
                $createdEvent = $service->events->insert('primary', $event);
                
                // Simpan Google Event ID
                $actionItem->update(['google_event_id' => $createdEvent->id]);
                
                Log::info("Admin successfully synced action item", [
                    'action_id' => $actionItem->action_id,
                    'event_id' => $createdEvent->id
                ]);
                
                $synced++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Admin failed to sync action item {$actionItem->action_id}: " . $e->getMessage(), [
                    'action_id' => $actionItem->action_id,
                    'item' => $actionItem->item ?? 'N/A',
                    'due' => $actionItem->due ?? 'N/A',
                    'exception' => get_class($e)
                ]);
            }
        }
        
        Log::info("Admin sync completed", [
            'admin_id' => $user->id,
            'synced' => $synced,
            'failed' => $failed,
            'total' => $actionItems->count()
        ]);
        
        return $synced;
    }

    /**
     * Update existing event in Google Calendar
     * Signature disesuaikan dengan Observer: updateEvent($eventId, $actionItem, $user)
     */
    public function updateEvent($eventId, $actionItem, $user)
    {
        try {
            $client = $this->getClient($user);
            $service = new Google_Service_Calendar($client);

            // Load mom relation jika belum
            if (!$actionItem->relationLoaded('mom')) {
                $actionItem->load('mom');
            }

            $event = $service->events->get('primary', $eventId);
            
            // Format tanggal ke Y-m-d
            $dueDate = Carbon::parse($actionItem->due)->format('Y-m-d');
            
            $event->setSummary($actionItem->item);
            $event->setDescription("Dari MoM: {$actionItem->mom->title}");
            
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDate($dueDate);
            $start->setTimeZone('Asia/Jakarta');
            $event->setStart($start);
            
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDate($dueDate);
            $end->setTimeZone('Asia/Jakarta');
            $event->setEnd($end);

            $updatedEvent = $service->events->update('primary', $eventId, $event);
            
            Log::info("Google Calendar event updated", [
                'event_id' => $eventId,
                'action_id' => $actionItem->action_id,
                'user_id' => $user->id,
                'due_date' => $dueDate
            ]);
            
            return $updatedEvent->id;
        } catch (\Exception $e) {
            Log::error("Failed to update Google Calendar event: " . $e->getMessage(), [
                'event_id' => $eventId,
                'action_id' => $actionItem->action_id ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Revoke Google Calendar access
     */
    public function revokeAccess($user)
    {
        try {
            if ($user->google_access_token) {
                $token = json_decode($user->google_access_token, true);
                $this->client->revokeToken($token);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to revoke Google Calendar access: " . $e->getMessage());
            return false;
        }
    }
}