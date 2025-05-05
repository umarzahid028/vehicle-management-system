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
        Schema::create('goodwill_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_issue_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('issue_description');
            $table->text('requested_resolution');
            $table->boolean('customer_consent')->default(false);
            $table->timestamp('customer_consent_date')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, in_progress, completed
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goodwill_claims');
    }
}; 