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
        Schema::table('vendors', function (Blueprint $table) {
            // First convert existing single values to JSON arrays
            DB::statement("UPDATE vendors SET specialty_tags = JSON_ARRAY(specialty_tags) WHERE specialty_tags IS NOT NULL");
            
            // Change the column type to JSON
            $table->json('specialty_tags')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Convert back to string by taking first value from array
            DB::statement("UPDATE vendors SET specialty_tags = JSON_UNQUOTE(JSON_EXTRACT(specialty_tags, '$[0]')) WHERE specialty_tags IS NOT NULL");
            
            // Change back to string
            $table->string('specialty_tags')->nullable()->change();
        });
    }
}; 