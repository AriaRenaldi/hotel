<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('pending_check_out_at')->nullable()->after('actual_check_out_at');
            $table->unsignedInteger('pending_late_checkout_hours')->default(0)->after('pending_check_out_at');
            $table->decimal('pending_late_checkout_penalty', 10, 2)->default(0)->after('pending_late_checkout_hours');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_type', ['booking', 'late_checkout_penalty'])
                ->default('booking')
                ->after('booking_id');
        });

        DB::statement("ALTER TABLE payments ADD INDEX payments_payment_type_status_idx (payment_type, status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments DROP INDEX payments_payment_type_status_idx");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'pending_check_out_at',
                'pending_late_checkout_hours',
                'pending_late_checkout_penalty',
            ]);
        });
    }
};

