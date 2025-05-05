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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('sales_team_id')->nullable()->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('assigned_for_sale_by')->nullable()->after('sales_team_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('assigned_for_sale_at')->nullable()->after('assigned_for_sale_by');
            
            // Add a new status option for "ready_for_sale" if it doesn't exist
            // We'll let the code handle this since we don't have access to enum values in migrations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['sales_team_id']);
            $table->dropForeign(['assigned_for_sale_by']);
            $table->dropColumn(['sales_team_id', 'assigned_for_sale_by', 'assigned_for_sale_at']);
        });
    }
};
