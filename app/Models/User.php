<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
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

      /** URL siap pakai untuk <img> */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar) && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar); // /storage/avatars/xxx.jpg
        }
        // fallback (bebas: pakai asset lokal atau URL publik)
        return asset('img/avatar-default.png');
    }
}
