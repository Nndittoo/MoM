<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomStatus extends Model
{
    use HasFactory;
    
    protected $table = 'mom_status';
    protected $primaryKey = 'status_id';

    protected $fillable = ['status'];
    
    public function moms()
    {
        return $this->hasMany(Mom::class, 'status_id');
    }
}