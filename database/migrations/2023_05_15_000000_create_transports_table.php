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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('origin')->nullable();
            $table->string('destination');
            $table->date('pickup_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_transit, delivered, cancelled
            $table->string('transporter_name')->nullable();
            $table->string('transporter_phone')->nullable();
            $table->string('transporter_email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
}; 