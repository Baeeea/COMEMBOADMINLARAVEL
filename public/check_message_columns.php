<?php
/**
 * Script to check and verify the message table structure and data
 */

// Include Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Use Laravel's DB facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if the messages table exists
$messagesTableExists = Schema::hasTable('messages');
echo "Messages table exists: " . ($messagesTableExists ? "YES ✓" : "NO ✗") . "\n";

if ($messagesTableExists) {
    // Check for the required columns
    $senderTypeExists = Schema::hasColumn('messages', 'sender_type');
    $receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');
    
    echo "sender_type column exists: " . ($senderTypeExists ? "YES ✓" : "NO ✗") . "\n";
    echo "receiver_type column exists: " . ($receiverTypeExists ? "YES ✓" : "NO ✗") . "\n";
    
    // Get column information
    $columns = DB::select("SHOW COLUMNS FROM messages");
    echo "\nColumns in messages table:\n";
    echo "------------------------\n";
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} " . ($column->Null === "YES" ? "(nullable)" : "(not null)");
        if ($column->Default !== null) {
            echo " default: '{$column->Default}'";
        }
        echo "\n";
    }
    
    // Check message counts
    $totalMessages = DB::table('messages')->count();
    echo "\nMessage statistics:\n";
    echo "------------------\n";
    echo "Total messages: $totalMessages\n";
    
    // Check for messages with null sender_type or receiver_type
    if ($senderTypeExists && $receiverTypeExists) {
        $nullSenderType = DB::table('messages')->whereNull('sender_type')->count();
        $nullReceiverType = DB::table('messages')->whereNull('receiver_type')->count();
        
        echo "Messages with null sender_type: $nullSenderType\n";
        echo "Messages with null receiver_type: $nullReceiverType\n";
        
        // Fix null types if needed
        if ($nullSenderType > 0 || $nullReceiverType > 0) {
            echo "\nFixing null types...\n";
            $updated = DB::table('messages')
                ->whereNull('sender_type')
                ->orWhereNull('receiver_type')
                ->update([
                    'sender_type' => 'App\\Models\\User',
                    'receiver_type' => 'App\\Models\\User'
                ]);
            
            echo "Updated $updated records with default type values ✓\n";
        }
    }
    
    // Check for potential UTF-8 encoding issues in message content
    if (DB::connection()->getDriverName() === 'mysql') {
        try {
            $potentialIssues = DB::select("
                SELECT COUNT(*) as count FROM messages 
                WHERE message IS NOT NULL 
                AND message <> '' 
                AND message <> CONVERT(message USING utf8mb4)
            ");
            
            echo "\nMessages with potential UTF-8 encoding issues: {$potentialIssues[0]->count}\n";
        } catch (\Exception $e) {
            echo "\nCouldn't check UTF-8 encoding issues: {$e->getMessage()}\n";
        }
    }
    
    echo "\nCheck complete! ✓\n";
} else {
    echo "Cannot check columns because messages table does not exist.\n";
    
    // Display what tables do exist
    $tables = DB::select('SHOW TABLES');
    echo "\nExisting tables in database:\n";
    foreach ($tables as $table) {
        $tableNames = get_object_vars($table);
        echo "- " . reset($tableNames) . "\n";
    }
}
