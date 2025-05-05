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
        Schema::table('inspection_item_results', function (Blueprint $table) {
            $table->string('diagnostic_status')->nullable()->after('repair_completed');
            $table->boolean('is_vendor_visible')->default(false)->after('diagnostic_status');
            $table->timestamp('assigned_at')->nullable()->after('is_vendor_visible');
            $table->timestamp('completed_at')->nullable()->after('assigned_at');
            $table->string('photo_path')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_item_results', function (Blueprint $table) {
            $table->dropColumn(['diagnostic_status', 'is_vendor_visible', 'assigned_at', 'completed_at', 'photo_path']);
        });
    }
};
