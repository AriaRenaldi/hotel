<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('hotel_facilities', 'icon')) {
            Schema::table('hotel_facilities', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('hotel_facilities', 'icon')) {
            Schema::table('hotel_facilities', function (Blueprint $table) {
                $table->string('icon')->nullable();
            });
        }
    }
};
