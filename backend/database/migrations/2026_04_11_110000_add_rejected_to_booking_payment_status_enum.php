<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE bookings
            MODIFY payment_status ENUM('pending','paid','verified','rejected','cancelled')
            NOT NULL DEFAULT 'pending'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE bookings SET payment_status = 'pending' WHERE payment_status = 'rejected'");

        DB::statement("
            ALTER TABLE bookings
            MODIFY payment_status ENUM('pending','paid','verified','cancelled')
            NOT NULL DEFAULT 'pending'
        ");
    }
};

