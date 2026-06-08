<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use ResolvesPublicStorageUrl;

    protected $fillable = [
        'type_name',
        'description',
        'price_per_night',
        'capacity',
        'facilities',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getImageUrlAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }
}
