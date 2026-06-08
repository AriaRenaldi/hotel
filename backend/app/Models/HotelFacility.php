<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    use ResolvesPublicStorageUrl;

    protected $fillable = [
        'facility_name',
        'description',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }
}
