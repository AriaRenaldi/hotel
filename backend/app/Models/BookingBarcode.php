<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBarcode extends Model
{
    protected $fillable = [
        'booking_id',
        'barcode_number',
        'barcode_type',
        'barcode_data',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}

