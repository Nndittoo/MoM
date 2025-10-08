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
        'pimpinan_rapat',      
        'notulen',    
        'creator_id',
        'pembahasan',
        'status_id',
        'nama_peserta', 
        'nama_mitra',
    ];

    // Casting untuk kolom JSON
    protected $casts = [
        'nama_peserta' => 'array',
        'nama_mitra' => 'array',
    ];
    
    // Relasi ke User
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
    
    // Relasi ke Status
    public function status()
    {
        return $this->belongsTo(MomStatus::class, 'status_id', 'status_id');
    }
    
    // Relasi ke Tindak Lanjut (Action Items)
    public function actionItems()
    {
        return $this->hasMany(ActionItem::class, 'mom_id', 'version_id');
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