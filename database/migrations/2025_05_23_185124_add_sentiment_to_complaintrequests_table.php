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
            $table->enum('sentiment', ['positive', 'negative', 'neutral'])->default('neutral')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaintrequests', function (Blueprint $table) {
            $table->dropColumn('sentiment');
        });
    }
};
