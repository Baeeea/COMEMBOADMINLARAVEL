<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to check for column existence
     */
    public function up(): void
    {
        // Check if the columns exist in the messages table
        $senderTypeExists = Schema::hasColumn('messages', 'sender_type');
        $receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');
        
        // Output the results
        echo "Database Check Results:\n";
        echo "---------------------\n";
        echo "✓ 'sender_type' column exists: " . ($senderTypeExists ? "YES ✓" : "NO ✗") . "\n";
        echo "✓ 'receiver_type' column exists: " . ($receiverTypeExists ? "YES ✓" : "NO ✗") . "\n";
        
        // If columns don't exist, add them
        if (!$senderTypeExists || !$receiverTypeExists) {
            echo "\nAdding missing columns...\n";
            Schema::table('messages', function (Blueprint $table) use ($senderTypeExists, $receiverTypeExists) {
                if (!$senderTypeExists) {
                    $table->string('sender_type')->default('App\\Models\\User')->after('sender_id');
                    echo "✓ Added 'sender_type' column\n";
                }
                
                if (!$receiverTypeExists) {
                    $table->string('receiver_type')->default('App\\Models\\User')->after('receiver_id');
                    echo "✓ Added 'receiver_type' column\n";
                }
            });
        }
        
        // Update any existing null values
        $affectedRows = DB::table('messages')
            ->whereNull('sender_type')
            ->orWhereNull('receiver_type')
            ->update([
                'sender_type' => 'App\\Models\\User',
                'receiver_type' => 'App\\Models\\User'
            ]);
        
        echo "\nUpdated " . $affectedRows . " existing message records with default type values\n";
        
        // Verify message count
        $messageCount = DB::table('messages')->count();
        echo "Total messages in database: " . $messageCount . "\n";
        
        // Check for UTF-8 encoding issues
        $invalidMessages = DB::table('messages')
            ->whereRaw("message IS NOT NULL AND message <> ''")
            ->whereRaw("message <> CONVERT(message USING utf8mb4)")
            ->count();
        
        echo "Messages with potential UTF-8 encoding issues: " . $invalidMessages . "\n";
        
        echo "\nDatabase check complete!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a check migration, nothing to reverse
    }
};
