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
            $table->decimal('actual_cost', 10, 2)->nullable()->after('cost');
            $table->text('completion_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_item_results', function (Blueprint $table) {
            $table->dropColumn(['actual_cost', 'completion_notes']);
        });
    }
};
