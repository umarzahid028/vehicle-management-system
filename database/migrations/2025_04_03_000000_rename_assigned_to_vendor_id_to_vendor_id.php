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
            $table->renameColumn('assigned_to_vendor_id', 'vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_item_results', function (Blueprint $table) {
            $table->renameColumn('vendor_id', 'assigned_to_vendor_id');
        });
    }
}; 