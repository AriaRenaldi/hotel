<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use ResolvesPublicStorageUrl;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'image_url',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function getImageUrlAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }
}
