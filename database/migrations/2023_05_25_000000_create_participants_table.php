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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('job_title')->nullable();
            $table->string('organization')->nullable();
            $table->string('role');
            $table->enum('category', ['general', 'invited', 'internal', 'coordinators']);
            $table->enum('payment_status', ['Paid via Vabu', 'Paid via M-Pesa', 'Complimentary', 'Not Applicable', 'Waived'])->default('Not Applicable');
            $table->boolean('payment_confirmed')->default(false);
            $table->boolean('day1_attended')->default(false);
            $table->boolean('day2_attended')->default(false);
            $table->boolean('day3_attended')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
}; 