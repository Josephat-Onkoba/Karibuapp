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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('checked_in_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Each participant can only be checked in once per day
            $table->unique(['participant_id', 'conference_day_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
