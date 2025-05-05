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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('name')->nullable();
            $table->string('status')->default('pending'); // pending, in_transit, delivered, cancelled
            $table->foreignId('transporter_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_pickup_date')->nullable();
            $table->date('scheduled_delivery_date')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add batch_id to transports table
        Schema::table('transports', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('transporter_id')
                  ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        
        Schema::dropIfExists('batches');
    }
}; 