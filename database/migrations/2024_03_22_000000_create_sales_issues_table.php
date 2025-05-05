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
        Schema::create('sales_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by_user_id')->constrained('users');
            $table->string('issue_type'); // 'damage', 'missed_issue', 'customer_reported'
            $table->text('description');
            $table->string('priority')->default('normal'); // 'low', 'normal', 'high', 'urgent'
            $table->string('status')->default('pending'); // 'pending', 'in_review', 'approved', 'rejected', 'completed'
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_issues');
    }
}; 