<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi untuk MoM yang dibuatnya (Creator)
    public function createdMoms()
    {
        return $this->hasMany(Mom::class, 'creator_id', 'id');
    }

    // Relasi untuk MoM di mana ia menjadi Pemimpin Rapat
    public function ledMoms()
    {
        return $this->hasMany(Mom::class, 'leader_id', 'id');
    }

    // Relasi untuk MoM di mana ia menjadi Notulen
    public function notedMoms()
    {
        return $this->hasMany(Mom::class, 'notulen_id', 'id');
    }

    // Relasi Peserta Rapat (Many-to-Many)
    public function attendedMoms()
    {
        // Parameter ketiga: Foreign key model ini (User) di tabel pivot (mom_attendees)
        return $this->belongsToMany(Mom::class, 'mom_attendees', 'user_id', 'mom_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
