<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('transporter_id')->nullable()->after('remember_token')
                  ->constrained()->nullOnDelete();
        });

        // Update existing users with transporter_id based on email
        DB::table('users')
            ->join('transporters', 'users.email', '=', 'transporters.email')
            ->update(['users.transporter_id' => DB::raw('transporters.id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropColumn('transporter_id');
        });
    }
}; 