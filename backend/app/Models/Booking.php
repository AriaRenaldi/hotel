<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicStorageUrl;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use ResolvesPublicStorageUrl;

    protected $appends = [
        'display_booking_status',
        'display_payment_status',
    ];

    protected $fillable = [
        'booking_number',
        'guest_id',
        'check_in_date',
        'requested_checkin_time',
        'checkin_approved_by',
        'checkin_approved_at',
        'check_out_date',
        'actual_check_in_at',
        'actual_check_out_at',
        'checkout_processed_by',
        'checkout_processed_at',
        'pending_check_out_at',
        'late_checkout_hours',
        'late_checkout_penalty',
        'pending_late_checkout_hours',
        'pending_late_checkout_penalty',
        'total_rooms',
        'total_nights',
        'subtotal',
        'tax',
        'total_amount',
        'payment_method',
        'payment_status',
        'booking_status',
        'payment_proof',
        'checkin_receipt_proof',
        'checkin_receipt_uploaded_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'actual_check_in_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'checkin_approved_at' => 'datetime',
        'checkout_processed_at' => 'datetime',
        'pending_check_out_at' => 'datetime',
        'checkin_receipt_uploaded_at' => 'datetime',
        'late_checkout_hours' => 'integer',
        'pending_late_checkout_hours' => 'integer',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'late_checkout_penalty' => 'decimal:2',
        'pending_late_checkout_penalty' => 'decimal:2',
    ];

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function checkInApprover()
    {
        return $this->belongsTo(User::class, 'checkin_approved_by');
    }

    public function checkOutProcessor()
    {
        return $this->belongsTo(User::class, 'checkout_processed_by');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function barcode()
    {
        return $this->hasOne(BookingBarcode::class);
    }

    public function getPaymentProofAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }

    public function getCheckinReceiptProofAttribute($value): ?string
    {
        return $this->resolvePublicStorageUrl($value);
    }

    public function getDisplayBookingStatusAttribute(): string
    {
        if ($this->booking_status === 'cancelled') {
            return 'Dibatalkan';
        }

        if ($this->booking_status === 'confirmed' && in_array($this->payment_status, ['pending', 'paid'], true)) {
            return 'Menunggu Pembayaran';
        }

        return match ($this->booking_status) {
            'confirmed' => 'Dikonfirmasi',
            'checked_in' => 'Check-in',
            'checked_out' => 'Selesai',
            default => ucfirst((string) $this->booking_status),
        };
    }

    public function getDisplayPaymentStatusAttribute(): string
    {
        if ($this->booking_status === 'cancelled' || $this->payment_status === 'cancelled') {
            return 'Dibatalkan';
        }

        return match ($this->payment_status) {
            'pending', 'paid' => 'Menunggu Pembayaran',
            'verified' => 'Lunas',
            default => ucfirst((string) $this->payment_status),
        };
    }
}
