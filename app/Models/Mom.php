<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mom extends Model
{
    use HasFactory;
    
    protected $table = 'moms';
    protected $primaryKey = 'version_id';
    public $incrementing = true;
    
    protected $fillable = [
        'title',
        'meeting_date',
        'location',
        'start_time',
        'end_time',
        'leader_id',      
        'notulen_id',     
        'creator_id',
        'pembahasan',
        'status_id',
    ];
    
    // Relasi ke User
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id', 'id');
    }
    public function notulen()
    {
        return $this->belongsTo(User::class, 'notulen_id', 'id');
    }

    // Relasi ke Status
    public function status()
    {
        return $this->belongsTo(MomStatus::class, 'status_id', 'status_id');
    }
    
    // Relasi ke Tindak Lanjut
    public function actionItems()
    {
        return $this->hasMany(ActionItem::class, 'mom_id', 'version_id');
    }

    // Relasi ke Peserta Rapat
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'mom_attendees', 'mom_id', 'user_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Relasi ke Lampiran
    public function attachments()
    {
        return $this->hasMany(MomAttachment::class, 'mom_id', 'version_id');
    }
    
    // Relasi ke Agenda
    public function agendas()
    {
        return $this->hasMany(MomAgenda::class, 'mom_id', 'version_id')->orderBy('order');
    }
}