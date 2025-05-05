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
        // Remove foreign key constraint on batch_id if exists
        if (Schema::hasColumn('transports', 'batch_id')) {
            // Check if foreign key exists by querying information_schema
            $foreignKeyExists = DB::select("
                SELECT COUNT(*) AS count FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'transports' 
                AND COLUMN_NAME = 'batch_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if ($foreignKeyExists[0]->count > 0) {
                // Drop foreign key constraints
                $constraintName = DB::select("
                    SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'transports' 
                    AND COLUMN_NAME = 'batch_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ")[0]->CONSTRAINT_NAME;
                
                Schema::table('transports', function (Blueprint $table) use ($constraintName) {
                    $table->dropForeign($constraintName);
                });
            }
            
            // Drop column to recreate it
            Schema::table('transports', function (Blueprint $table) {
                $table->dropColumn('batch_id');
            });
        }

        // Add new columns to transports table
        Schema::table('transports', function (Blueprint $table) {
            // Add new batch_id as string
            $table->string('batch_id')->nullable()->after('transporter_id');
            $table->string('batch_name')->nullable()->after('notes');
            $table->string('gate_pass_path')->nullable()->after('batch_name');
            $table->string('qr_code_path')->nullable()->after('gate_pass_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn(['batch_name', 'gate_pass_path', 'qr_code_path']);
            
            // If batch_id exists, drop it to recreate as integer with foreign key
            if (Schema::hasColumn('transports', 'batch_id')) {
                $table->dropColumn('batch_id');
            }
            
            // Recreate batch_id as integer with foreign key if batches table exists
            if (Schema::hasTable('batches')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('transporter_id');
                $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            }
        });
    }
}; 