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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('registered_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('day1_valid')->default(false);
            $table->boolean('day2_valid')->default(false);
            $table->boolean('day3_valid')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
}; 