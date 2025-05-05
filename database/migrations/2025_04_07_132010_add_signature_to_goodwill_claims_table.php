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
        Schema::table('goodwill_claims', function (Blueprint $table) {
            $table->longText('customer_signature')->nullable()->after('customer_consent_date');
            $table->boolean('signed_in_person')->default(false)->after('customer_signature');
            $table->timestamp('signature_date')->nullable()->after('signed_in_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goodwill_claims', function (Blueprint $table) {
            $table->dropColumn('customer_signature');
            $table->dropColumn('signed_in_person');
            $table->dropColumn('signature_date');
        });
    }
};
