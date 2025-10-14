<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
