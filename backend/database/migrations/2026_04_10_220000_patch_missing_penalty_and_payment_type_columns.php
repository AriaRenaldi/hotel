<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'pending_check_out_at')) {
                    $table->timestamp('pending_check_out_at')->nullable()->after('actual_check_out_at');
                }

                if (!Schema::hasColumn('bookings', 'pending_late_checkout_hours')) {
                    $table->unsignedInteger('pending_late_checkout_hours')->default(0)->after('actual_check_out_at');
                }

                if (!Schema::hasColumn('bookings', 'pending_late_checkout_penalty')) {
                    $table->decimal('pending_late_checkout_penalty', 10, 2)->default(0)->after('actual_check_out_at');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'payment_type')) {
                    $table->enum('payment_type', ['booking', 'late_checkout_penalty'])
                        ->default('booking')
                        ->after('booking_id');
                }
            });

            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'payments')
                ->where('index_name', 'payments_payment_type_status_idx')
                ->exists();

            if (!$indexExists) {
                DB::statement('ALTER TABLE payments ADD INDEX payments_payment_type_status_idx (payment_type, status)');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'payments')
                ->where('index_name', 'payments_payment_type_status_idx')
                ->exists();

            if ($indexExists) {
                DB::statement('ALTER TABLE payments DROP INDEX payments_payment_type_status_idx');
            }

            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'payment_type')) {
                    $table->dropColumn('payment_type');
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $columnsToDrop = [];

                if (Schema::hasColumn('bookings', 'pending_check_out_at')) {
                    $columnsToDrop[] = 'pending_check_out_at';
                }

                if (Schema::hasColumn('bookings', 'pending_late_checkout_hours')) {
                    $columnsToDrop[] = 'pending_late_checkout_hours';
                }

                if (Schema::hasColumn('bookings', 'pending_late_checkout_penalty')) {
                    $columnsToDrop[] = 'pending_late_checkout_penalty';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
