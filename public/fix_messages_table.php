<?php
/**
 * Script to add missing columns to the messages table
 */

// Include Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Use Laravel's Schema and DB facades
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

// Display header
echo "✓---------------------------------✓\n";
echo "✓ Messages Table Migration Helper ✓\n";
echo "✓---------------------------------✓\n\n";

// Check if the messages table exists
if (!Schema::hasTable('messages')) {
    echo "❌ Error: Messages table does not exist!\n";
    exit(1);
}

// Check for missing columns
$senderTypeExists = Schema::hasColumn('messages', 'sender_type');
$receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');

echo "Current status:\n";
echo "- sender_type column: " . ($senderTypeExists ? "EXISTS ✓" : "MISSING ❌") . "\n";
echo "- receiver_type column: " . ($receiverTypeExists ? "EXISTS ✓" : "MISSING ❌") . "\n\n";

// Add missing columns if needed
if (!$senderTypeExists || !$receiverTypeExists) {
    echo "Adding missing columns...\n";
    
    Schema::table('messages', function (Blueprint $table) use ($senderTypeExists, $receiverTypeExists) {
        if (!$senderTypeExists) {
            $table->string('sender_type')->default('App\\Models\\User')->after('sender_id');
            echo "- Added 'sender_type' column ✓\n";
        }
        
        if (!$receiverTypeExists) {
            $table->string('receiver_type')->default('App\\Models\\User')->after('receiver_id');
            echo "- Added 'receiver_type' column ✓\n";
        }
    });
    
    // Update existing records with default values
    $updated = DB::table('messages')
        ->whereNull('sender_type')
        ->orWhereNull('receiver_type')
        ->update([
            'sender_type' => 'App\\Models\\User',
            'receiver_type' => 'App\\Models\\User'
        ]);
    
    echo "- Updated $updated existing records with default values ✓\n";
    
    echo "\nMigration completed successfully! ✓\n";
} else {
    echo "All required columns already exist. No changes needed. ✓\n";
}

// Show table summary
echo "\nTable summary:\n";
$columns = DB::select("SHOW COLUMNS FROM messages");
foreach ($columns as $column) {
    echo "- {$column->Field}: {$column->Type}";
    if ($column->Default !== null) {
        echo " (default: '{$column->Default}')";
    }
    echo "\n";
}

echo "\n✓ Completed! ✓\n";
