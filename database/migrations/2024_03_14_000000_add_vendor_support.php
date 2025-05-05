<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('password');
            $table->string('vendor_type')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('vendor_type');
        });

        Schema::table('inspection_items', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('inspection_stage_id')->constrained('users')->nullOnDelete();
            $table->decimal('estimated_cost', 10, 2)->nullable()->after('vendor_id');
            $table->decimal('actual_cost', 10, 2)->nullable()->after('estimated_cost');
            $table->text('notes')->nullable()->after('actual_cost');
            $table->text('completion_notes')->nullable()->after('notes');
            $table->json('photos')->nullable()->after('completion_notes');
            $table->timestamp('estimate_submitted_at')->nullable()->after('photos');
            $table->timestamp('completed_at')->nullable()->after('estimate_submitted_at');
            $table->boolean('vendor_required')->default(false)->after('completed_at');
            $table->boolean('cost_tracking')->default(false)->after('vendor_required');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'vendor_type', 'is_active']);
        });

        Schema::table('inspection_items', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn([
                'vendor_id',
                'estimated_cost',
                'actual_cost',
                'notes',
                'completion_notes',
                'photos',
                'estimate_submitted_at',
                'completed_at',
                'vendor_required',
                'cost_tracking',
            ]);
        });
    }
}; 