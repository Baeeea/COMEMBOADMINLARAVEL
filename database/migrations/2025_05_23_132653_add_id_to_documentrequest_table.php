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
        Schema::table('documentrequest', function (Blueprint $table) {
            // Add auto-incrementing primary key if it doesn't exist
            if (!Schema::hasColumn('documentrequest', 'id')) {
                // First add the column
                $table->bigIncrements('id')->first();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentrequest', function (Blueprint $table) {
            // Drop the id column if it exists
            if (Schema::hasColumn('documentrequest', 'id')) {
                $table->dropColumn('id');
            }
        });
    }
};
