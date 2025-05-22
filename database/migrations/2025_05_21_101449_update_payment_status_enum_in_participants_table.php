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
        // This approach works in MySQL
        DB::statement("ALTER TABLE participants MODIFY COLUMN payment_status ENUM('Paid via Vabu', 'Paid via M-Pesa', 'Not Paid', 'Complimentary', 'Not Applicable', 'Waived') NOT NULL DEFAULT 'Not Paid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values
        DB::statement("ALTER TABLE participants MODIFY COLUMN payment_status ENUM('Paid via Vabu', 'Paid via M-Pesa', 'Complimentary', 'Not Applicable', 'Waived') NOT NULL DEFAULT 'Not Applicable'");
    }
};
