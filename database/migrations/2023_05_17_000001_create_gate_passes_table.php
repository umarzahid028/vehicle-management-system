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
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->string('pass_number')->unique();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transporter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, approved, used, expired, cancelled
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->string('authorized_by')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
}; 