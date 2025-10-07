<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomAgenda extends Model
{
    use HasFactory;
    
    protected $table = 'mom_agendas';
    protected $primaryKey = 'agenda_id';

    protected $fillable = [
        'mom_id',
        'item',
        'order',
    ];
    
    public function mom()
    {
        return $this->belongsTo(Mom::class, 'mom_id', 'version_id');
    }
}