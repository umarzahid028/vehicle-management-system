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
        Schema::table('inspection_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inspection_items', 'vendor_required')) {
                $table->boolean('vendor_required')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('inspection_items', 'cost_tracking')) {
                $table->boolean('cost_tracking')->default(false)->after('vendor_required');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_items', function (Blueprint $table) {
            $table->dropColumn(['vendor_required', 'cost_tracking']);
        });
    }
};
