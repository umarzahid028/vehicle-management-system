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
        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_stage_id')->constrained();
            $table->foreignId('user_id')->constrained()->comment('Inspector or technician');
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->dateTime('inspection_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_inspections');
    }
}; 