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
        DB::statement("ALTER TABLE inspection_item_results MODIFY COLUMN status ENUM('pass', 'fail', 'warning', 'not_applicable', 'completed', 'cancelled') DEFAULT 'not_applicable'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE inspection_item_results MODIFY COLUMN status ENUM('pass', 'fail', 'warning', 'not_applicable') DEFAULT 'not_applicable'");
    }
};
