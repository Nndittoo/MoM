<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'related_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get icon berdasarkan type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'mom_pending' => 'fa-solid fa-file-signature',
            'task_urgent' => 'fa-solid fa-triangle-exclamation',
            'user_new'    => 'fa-solid fa-user-plus',
            'task_overdue' => 'fa-solid fa-exclamation-triangle',
            default       => 'fa-solid fa-bell'
        };
    }

    /**
     * Get color berdasarkan type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'mom_pending' => 'blue',
            'task_urgent' => 'yellow',
            'user_new'    => 'green',
            'task_overdue' => 'red',
            default       => 'gray'
        };
    }

        // Method untuk mengirim FCM ke semua admin
    public function sendFcmToAdmins()
    {
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            $tokens = \App\Models\DeviceToken::whereIn('user_id', $admins->pluck('id'))
                ->pluck('token')
                ->unique()
                ->toArray();

            if (empty($tokens)) {
                Log::warning('No admin device tokens found for FCM notification');
                return false;
            }

            // Tentukan data tambahan berdasarkan tipe notifikasi
            $additionalData = [
                'type' => $this->type,
                'notification_id' => (string) $this->id,
                'related_id' => $this->related_id ? (string) $this->related_id : '',
            ];

            // Tambahkan URL redirect berdasarkan tipe
            if (in_array($this->type, ['task_urgent', 'task_overdue'])) {
                $additionalData['redirect_url'] = route('admin.task');
            } elseif ($this->type === 'mom_pending') {
                $additionalData['redirect_url'] = route('admin.shows', $this->related_id);
            } elseif ($this->type === 'user_new') {
                $additionalData['redirect_url'] = route('admin.users');
            }

            $fcm = app(FcmService::class);
            $result = $fcm->sendNotification($tokens, $this->title, $this->message, $additionalData);

            if ($result) {
                Log::info("FCM notification sent successfully", [
                    'type' => $this->type,
                    'notification_id' => $this->id,
                    'admin_count' => count($admins),
                    'token_count' => count($tokens)
                ]);
            }

            return $result;

        } catch (\Throwable $e) {
            Log::error('Failed sending FCM for AdminNotification: ' . $e->getMessage(), [
                'notification_id' => $this->id,
                'type' => $this->type,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    // Event handler setelah notifikasi dibuat
    protected static function booted()
    {
        static::created(function ($notification) {
            try {
                $notification->sendFcmToAdmins();
            } catch (\Throwable $e) {
                Log::error('Failed in booted event for AdminNotification: ' . $e->getMessage());
            }
        });
    }
}
