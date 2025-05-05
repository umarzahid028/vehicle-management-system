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
            $table->renameColumn('repair_cost', 'cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_item_results', function (Blueprint $table) {
            $table->renameColumn('cost', 'repair_cost');
        });
    }
}; 