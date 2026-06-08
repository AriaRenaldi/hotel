<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use ResolvesPublicStorageUrl;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'address',
        'role',
        'is_verified',
        'email_verified_at',
        'otp',
        'otp_expires_at',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->resolvePublicStorageUrl($this->attributes['photo'] ?? null);
    }
}
