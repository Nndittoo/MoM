<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
