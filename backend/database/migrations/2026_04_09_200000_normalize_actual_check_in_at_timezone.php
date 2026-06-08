<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')
            ->select('id', 'actual_check_in_at')
            ->whereNotNull('actual_check_in_at')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $normalized = Carbon::parse($row->actual_check_in_at)->subHours(7);

                    DB::table('bookings')
                        ->where('id', $row->id)
                        ->update(['actual_check_in_at' => $normalized]);
                }
            });
    }

    public function down(): void
    {
        DB::table('bookings')
            ->select('id', 'actual_check_in_at')
            ->whereNotNull('actual_check_in_at')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $restored = Carbon::parse($row->actual_check_in_at)->addHours(7);

                    DB::table('bookings')
                        ->where('id', $row->id)
                        ->update(['actual_check_in_at' => $restored]);
                }
            });
    }
};

