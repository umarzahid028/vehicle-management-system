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
        Schema::table('vendors', function (Blueprint $table) {
            // Rename type to specialty_tags
            $table->renameColumn('type', 'specialty_tags');
            
            // Add type_id column
            $table->unsignedBigInteger('type_id')->nullable();
            
            // Add foreign key constraint
            $table->foreign('type_id')
                  ->references('id')
                  ->on('vendor_types')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
            
            // Rename specialty_tags back to type
            $table->renameColumn('specialty_tags', 'type');
        });
    }
}; 