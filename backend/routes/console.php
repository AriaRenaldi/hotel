<?php

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:auto-checkin', function () {
    $today = now('Asia/Jakarta')->toDateString();
    $processed = 0;

    Booking::with('bookingDetails')
        ->whereDate('check_in_date', '<=', $today)
        ->whereDate('check_out_date', '>', $today)
        ->where('booking_status', 'confirmed')
        ->where('payment_method', 'cash')
        ->where('payment_status', 'verified')
        ->chunkById(100, function ($bookings) use (&$processed) {
            foreach ($bookings as $booking) {
                DB::transaction(function () use ($booking, &$processed) {
                    $booking->update([
                        'booking_status' => 'checked_in',
                        'actual_check_in_at' => $booking->actual_check_in_at ?? now(),
                    ]);

                    $roomIds = $booking->bookingDetails
                        ->pluck('room_id')
                        ->filter()
                        ->all();

                    if (!empty($roomIds)) {
                        Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);
                    }

                    $processed++;
                });
            }
        });

    $this->info("Auto check-in selesai. Booking terproses: {$processed}.");
})->purpose('Auto check-in booking cash aktif tanpa batasan jam');

Schedule::command('bookings:auto-checkin')
    ->everyMinute()
    ->withoutOverlapping();
