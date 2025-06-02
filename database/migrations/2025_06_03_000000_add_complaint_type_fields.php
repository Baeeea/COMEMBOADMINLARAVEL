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
            // For Theft complaints
            $table->string('items_stolen')->nullable()->after('explanation');
            $table->decimal('items_value', 10, 2)->nullable()->after('items_stolen');
            
            // For Illegal Business complaints
            $table->string('business_name')->nullable()->after('items_value');
            
            // For Illegal Parking complaints
            $table->string('vehicle_details')->nullable()->after('business_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaintrequests', function (Blueprint $table) {
            $table->dropColumn([
                'items_stolen',
                'items_value',
                'business_name',
                'vehicle_details'
            ]);
        });
    }
};
