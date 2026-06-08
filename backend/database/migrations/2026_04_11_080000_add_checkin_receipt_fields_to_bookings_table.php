<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'checkin_receipt_proof')) {
                $table->string('checkin_receipt_proof')->nullable()->after('payment_proof');
            }

            if (!Schema::hasColumn('bookings', 'checkin_receipt_uploaded_at')) {
                $table->timestamp('checkin_receipt_uploaded_at')->nullable()->after('checkin_receipt_proof');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'checkin_receipt_uploaded_at')) {
                $table->dropColumn('checkin_receipt_uploaded_at');
            }
            if (Schema::hasColumn('bookings', 'checkin_receipt_proof')) {
                $table->dropColumn('checkin_receipt_proof');
            }
        });
    }
};
