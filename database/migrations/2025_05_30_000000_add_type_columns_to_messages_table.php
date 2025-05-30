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
        Schema::table('messages', function (Blueprint $table) {
            // Add sender_type and receiver_type columns
            $table->string('sender_type')->default('App\\Models\\User')->after('sender_id');
            $table->string('receiver_type')->default('App\\Models\\User')->after('receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender_type');
            $table->dropColumn('receiver_type');
        });
    }
};
