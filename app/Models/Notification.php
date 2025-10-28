<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FcmService;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mom_id',
        'type',
        'title',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Mom
     */
    public function mom()
    {
        return $this->belongsTo(Mom::class, 'mom_id', 'version_id');
    }

    /**
     * Get icon berdasarkan type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'created' => 'fa-file-circle-plus',
            'approved' => 'fa-circle-check',
            'rejected' => 'fa-circle-xmark',
            default => 'fa-bell'
        };
    }

    /**
     * Get color berdasarkan type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'created' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get URL berdasarkan type
     */
    public function getUrlAttribute()
    {
        return match($this->type) {
            'rejected' => url("/moms/{$this->mom_id}/edit"),
            default => url("/moms/{$this->mom_id}")
        };
    }

    // Method untuk mengirim FCM ke user tertentu
    public function sendFcmToUser()
    {
        $tokens = \App\Models\DeviceToken::where('user_id', $this->user_id)
            ->pluck('token')
            ->unique()
            ->toArray();

        if (empty($tokens)) {
            return false;
        }

        $fcm = app(FcmService::class);
        return $fcm->sendNotification($tokens, $this->title, $this->message, [
            'type' => $this->type,
            'notification_id' => (string) $this->id,
            'mom_id' => $this->mom_id ? (string) $this->mom_id : '',
        ]);
    }

    // Event handler setelah notifikasi dibuat
    protected static function booted()
    {
        static::created(function ($notification) {
            try {
                $notification->sendFcmToUser();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed sending FCM for User Notification: ' . $e->getMessage());
            }
        });
    }
}
