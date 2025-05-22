<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        
        // Insert default meal types
        DB::table('meal_types')->insert([
            ['name' => 'Breakfast', 'description' => 'Morning meal', 'start_time' => '07:00:00', 'end_time' => '09:00:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lunch', 'description' => 'Midday meal', 'start_time' => '12:00:00', 'end_time' => '14:00:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Networking Tea', 'description' => 'Afternoon refreshments', 'start_time' => '16:00:00', 'end_time' => '17:00:00', 'active' => true, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_types');
    }
};
