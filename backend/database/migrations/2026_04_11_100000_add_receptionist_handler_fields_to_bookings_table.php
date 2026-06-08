<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'checkin_approved_by')) {
                $table->unsignedBigInteger('checkin_approved_by')->nullable()->after('requested_checkin_time');
            }

            if (!Schema::hasColumn('bookings', 'checkin_approved_at')) {
                $table->timestamp('checkin_approved_at')->nullable()->after('checkin_approved_by');
            }

            if (!Schema::hasColumn('bookings', 'checkout_processed_by')) {
                $table->unsignedBigInteger('checkout_processed_by')->nullable()->after('actual_check_out_at');
            }

            if (!Schema::hasColumn('bookings', 'checkout_processed_at')) {
                $table->timestamp('checkout_processed_at')->nullable()->after('checkout_processed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'checkout_processed_at')) {
                $table->dropColumn('checkout_processed_at');
            }
            if (Schema::hasColumn('bookings', 'checkout_processed_by')) {
                $table->dropColumn('checkout_processed_by');
            }
            if (Schema::hasColumn('bookings', 'checkin_approved_at')) {
                $table->dropColumn('checkin_approved_at');
            }
            if (Schema::hasColumn('bookings', 'checkin_approved_by')) {
                $table->dropColumn('checkin_approved_by');
            }
        });
    }
};
