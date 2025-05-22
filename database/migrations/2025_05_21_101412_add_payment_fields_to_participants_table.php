<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Drop the day attended fields as they're now handled by the check-ins table
            $table->dropColumn(['day1_attended', 'day2_attended', 'day3_attended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Add back the day attended fields
            $table->boolean('day1_attended')->default(false);
            $table->boolean('day2_attended')->default(false);
            $table->boolean('day3_attended')->default(false);
        });
    }
};
