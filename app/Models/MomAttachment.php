<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomAttachment extends Model
{
    use HasFactory;
    protected $table = 'mom_attachments';
    protected $primaryKey = 'attachment_id';
    
    protected $fillable = [
        'mom_id', 
        'file_name', 
        'file_path', 
        'mime_type', 
        'file_size', 
        'uploader_id'
    ];
    
    public function mom()
    {
        return $this->belongsTo(Mom::class, 'mom_id', 'version_id');
    }
    
    // Relasi ke Uploader (User)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id', 'id');
    }
}
