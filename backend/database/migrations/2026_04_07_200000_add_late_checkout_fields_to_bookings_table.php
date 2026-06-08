<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('actual_check_out_at')->nullable()->after('check_out_date');
            $table->unsignedInteger('late_checkout_hours')->default(0)->after('actual_check_out_at');
            $table->decimal('late_checkout_penalty', 10, 2)->default(0)->after('late_checkout_hours');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'actual_check_out_at',
                'late_checkout_hours',
                'late_checkout_penalty',
            ]);
        });
    }
};

