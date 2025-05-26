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
            // Change contact_number from integer to string (VARCHAR)
            $table->string('contact_number', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentrequest', function (Blueprint $table) {
            // Change back to integer if needed
            $table->integer('contact_number')->nullable()->change();
        });
    }
};
