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
        Schema::create('inspection_item_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_inspection_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_item_id')->constrained();
            $table->enum('status', ['pending', 'diagnostic', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('repair_cost', 10, 2)->default(0);
            $table->boolean('requires_repair')->default(false);
            $table->boolean('repair_completed')->default(false);
            $table->foreignId('assigned_to_vendor_id')->nullable()->constrained('vendors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_item_results');
    }
}; 