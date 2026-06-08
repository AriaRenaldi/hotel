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
        // Normalize legacy value before tightening enum options.
        DB::statement("UPDATE bookings SET payment_method = 'qris' WHERE payment_method = 'transfer'");
        DB::statement("UPDATE payments SET payment_method = 'qris' WHERE payment_method = 'transfer'");

        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('qris','cash') NOT NULL DEFAULT 'qris'");
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('qris','cash') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE bookings SET payment_method = 'transfer' WHERE payment_method = 'qris'");
        DB::statement("UPDATE payments SET payment_method = 'transfer' WHERE payment_method = 'qris'");

        DB::statement("ALTER TABLE bookings MODIFY payment_method ENUM('transfer','cash') NOT NULL DEFAULT 'transfer'");
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('transfer','cash') NOT NULL");
    }
};

