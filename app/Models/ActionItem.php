<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionItem extends Model
{
    use HasFactory;

    protected $table = 'action_items';
    protected $primaryKey = 'action_id'; 

    protected $fillable = [
        'mom_id',
        'item',
        'due',
        'status',
    ];

    protected $casts = [
        'due' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'action_id'; 
    }

    public function mom()
    {
        return $this->belongsTo(Mom::class, 'mom_id', 'version_id');
    }

    // Scope untuk task yang akan datang
    public function scopeMendatang($query)
    {
        return $query->where('status', 'mendatang');
    }

    // Scope untuk task yang selesai
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Scope untuk task yang hampir deadline (7 hari ke depan)
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due', '>=', now())
                     ->where('due', '<=', now()->addDays($days))
                     ->where('status', 'mendatang');
    }
}