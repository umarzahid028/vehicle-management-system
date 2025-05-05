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
        Schema::create('vehicle_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->text('image_url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            // Index
            $table->index('vehicle_id');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_images');
    }
};
