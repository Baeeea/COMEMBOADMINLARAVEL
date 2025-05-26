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
        Schema::table('complaintrequests', function (Blueprint $table) {
            // Add timestamps if they don't exist
            if (!Schema::hasColumn('complaintrequests', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('complaintrequests', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Update existing records with current timestamp
        DB::table('complaintrequests')->update([
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaintrequests', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
