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
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add transporter_id to transports table
        Schema::table('transports', function (Blueprint $table) {
            $table->foreignId('transporter_id')->nullable()->after('vehicle_id')
                  ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropColumn('transporter_id');
        });
        
        Schema::dropIfExists('transporters');
    }
}; 