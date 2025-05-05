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
        Schema::table('transports', function (Blueprint $table) {
            $table->boolean('is_acknowledged')->default(false)->after('status');
            $table->timestamp('acknowledged_at')->nullable()->after('is_acknowledged');
            $table->foreignId('acknowledged_by')->nullable()->after('acknowledged_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['acknowledged_by']);
            $table->dropColumn(['is_acknowledged', 'acknowledged_at', 'acknowledged_by']);
        });
    }
};
