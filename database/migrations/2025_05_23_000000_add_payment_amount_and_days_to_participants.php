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
        Schema::table('participants', function (Blueprint $table) {
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_confirmed');
            $table->integer('eligible_days')->nullable()->after('payment_amount');
            // Update category enum to include new categories
            DB::statement("ALTER TABLE participants MODIFY COLUMN category ENUM('general', 'invited', 'internal', 'coordinators', 'exhibitor', 'presenter') NOT NULL");
            // Add presenter type column
            $table->enum('presenter_type', ['non_student', 'student', 'international'])->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('payment_amount');
            $table->dropColumn('eligible_days');
            $table->dropColumn('presenter_type');
            // Revert category enum
            DB::statement("ALTER TABLE participants MODIFY COLUMN category ENUM('general', 'invited', 'internal', 'coordinators') NOT NULL");
        });
    }
}; 