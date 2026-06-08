<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use ResolvesPublicStorageUrl;

    protected $fillable = [
        'booking_id',
        'payment_type',
        'amount',
        'payment_method',
        'payment_date',
        'proof_image',
        'verified_by',
        'verified_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function getProofImageAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
