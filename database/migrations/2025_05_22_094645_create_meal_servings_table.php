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
        Schema::create('meal_servings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('served_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('ticket_number');
            $table->timestamp('served_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a participant can only be served a specific meal once per day
            // Use a shorter index name to avoid MySQL's 64 character limit
            $table->unique(['participant_id', 'meal_type_id', 'conference_day_id'], 'meal_serving_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_servings');
    }
};
