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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('stock_number')->unique();
            $table->string('vin')->unique();
            $table->integer('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('trim')->nullable();
            $table->date('date_in_stock')->nullable();
            $table->integer('odometer')->nullable();
            $table->string('exterior_color')->nullable();
            $table->string('interior_color')->nullable();
            $table->integer('number_of_leads')->nullable();
            $table->string('status')->nullable();
            $table->string('body_type')->nullable();
            $table->string('drive_train')->nullable();
            $table->string('engine')->nullable();
            $table->string('fuel_type')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_video')->default(false);
            $table->integer('number_of_pics')->nullable();
            $table->string('purchased_from')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('transmission')->nullable();
            $table->string('transmission_type')->nullable();
            $table->string('vehicle_purchase_source')->nullable();
            $table->decimal('advertising_price', 10, 2)->nullable();
            $table->string('deal_status')->nullable();
            $table->date('sold_date')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('import_file')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
