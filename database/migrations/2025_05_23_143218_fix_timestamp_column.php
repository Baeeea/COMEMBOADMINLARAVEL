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
        // First, update any invalid timestamp values to the current time
        DB::statement("UPDATE documentrequest SET timestamp = NOW() WHERE timestamp = '0000-00-00 00:00:00' OR timestamp IS NULL");

        // Then modify the timestamp column to allow NULL values
        Schema::table('documentrequest', function (Blueprint $table) {
            $table->timestamp('timestamp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentrequest', function (Blueprint $table) {
            $table->timestamp('timestamp')->nullable(false)->change();
        });
    }
};
