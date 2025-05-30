<?php
/**
 * Script to test message sending functionality
 */

// Include Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Use Laravel's facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;
use App\Models\User;

// Display header
echo "✓----------------------------✓\n";
echo "✓ Message Functionality Test ✓\n";
echo "✓----------------------------✓\n\n";

// Check if columns exist
$senderTypeExists = Schema::hasColumn('messages', 'sender_type');
$receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');

echo "Table structure checks:\n";
echo "- Messages table exists: " . (Schema::hasTable('messages') ? "YES ✓" : "NO ❌") . "\n";
echo "- sender_type column exists: " . ($senderTypeExists ? "YES ✓" : "NO ❌") . "\n";
echo "- receiver_type column exists: " . ($receiverTypeExists ? "YES ✓" : "NO ❌") . "\n\n";

// Get users for testing
$users = User::take(2)->get();

if ($users->count() < 2) {
    echo "❌ Error: Need at least 2 users for testing\n";
    exit(1);
}

$sender = $users[0];
$receiver = $users[1];

echo "Test users:\n";
echo "- Sender: {$sender->name} (ID: {$sender->id})\n";
echo "- Receiver: {$receiver->name} (ID: {$receiver->id})\n\n";

echo "Creating test message...\n";

try {
    // Login as sender
    Auth::login($sender);
    
    // Create and save a test message
    $message = new Message();
    $message->sender_id = $sender->id;
    $message->receiver_id = $receiver->id;
    
    // Set types if columns exist
    if ($senderTypeExists) {
        $message->sender_type = 'App\\Models\\User';
    }
    if ($receiverTypeExists) {
        $message->receiver_type = 'App\\Models\\User';
    }
    
    // Set message content with special UTF-8 characters to test encoding
    $message->message = "Test message with UTF-8 characters: áéíóú ñ € © ®";
    $message->is_read = false;
    $message->save();
    
    echo "- Message created successfully with ID: {$message->id} ✓\n";
    
    // Verify message was saved correctly
    $savedMessage = Message::find($message->id);
    
    echo "- Message retrieved successfully ✓\n";
    echo "- Content integrity check: " . 
        ($savedMessage->message === $message->message ? "PASSED ✓" : "FAILED ❌") . "\n";
    
    if ($senderTypeExists) {
        echo "- sender_type saved correctly: " . 
            ($savedMessage->sender_type === 'App\\Models\\User' ? "YES ✓" : "NO ❌") . "\n";
    }
    if ($receiverTypeExists) {
        echo "- receiver_type saved correctly: " . 
            ($savedMessage->receiver_type === 'App\\Models\\User' ? "YES ✓" : "NO ❌") . "\n";
    }
    
    echo "\nTest completed successfully! ✓\n";
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}:{$e->getLine()}\n";
}
