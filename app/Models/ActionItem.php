<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'due' => 'datetime',
    ];

    public function setDueAttribute($value)
    {
        if ($value) {
            // Convert ke Carbon instance jika belum
            $date = Carbon::parse($value);
            // Set waktu menjadi 23:59:59
            $this->attributes['due'] = $date->setHours(23)->setMinutes(59)->setSeconds(59);
        } else {
            $this->attributes['due'] = null;
        }
    }

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
