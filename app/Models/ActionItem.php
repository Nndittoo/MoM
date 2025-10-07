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
    
    public function mom()
    {
        return $this->belongsTo(Mom::class, 'mom_id', 'version_id');
    }
}