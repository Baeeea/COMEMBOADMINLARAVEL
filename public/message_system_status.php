<?php
/**
 * Script to display the message system status
 * This provides a visual dashboard of all the checks
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

// HTML header
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .warning {
            color: #e67e22;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .check-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .check-icon {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .fix-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .fix-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Messaging System Status Dashboard</h1>
        <div class="card">
            <h2>Database Structure</h2>';

// Check table structure
$messagesTableExists = Schema::hasTable('messages');
$senderTypeExists = $messagesTableExists && Schema::hasColumn('messages', 'sender_type');
$receiverTypeExists = $messagesTableExists && Schema::hasColumn('messages', 'receiver_type');

echo '<div class="check-item">
        <span class="check-icon">'.($messagesTableExists ? '✅' : '❌').'</span>
        <span>Messages table exists: <span class="'.($messagesTableExists ? 'success' : 'error').'">'.($messagesTableExists ? 'YES' : 'NO').'</span></span>
      </div>';

if ($messagesTableExists) {
    echo '<div class="check-item">
            <span class="check-icon">'.($senderTypeExists ? '✅' : '❌').'</span>
            <span>sender_type column exists: <span class="'.($senderTypeExists ? 'success' : 'error').'">'.($senderTypeExists ? 'YES' : 'NO').'</span></span>
          </div>';
    
    echo '<div class="check-item">
            <span class="check-icon">'.($receiverTypeExists ? '✅' : '❌').'</span>
            <span>receiver_type column exists: <span class="'.($receiverTypeExists ? 'success' : 'error').'">'.($receiverTypeExists ? 'YES' : 'NO').'</span></span>
          </div>';
    
    // If any columns are missing
    if (!$senderTypeExists || !$receiverTypeExists) {
        echo '<a href="fix_messages_table.php" class="fix-button">Fix Missing Columns</a>';
    }
    
    // Display table structure
    echo '<h3>Table Structure</h3>
          <table>
            <tr>
                <th>Column</th>
                <th>Type</th>
                <th>Nullable</th>
                <th>Default</th>
            </tr>';
    
    $columns = DB::select("SHOW COLUMNS FROM messages");
    foreach ($columns as $column) {
        echo '<tr>
                <td>'.$column->Field.'</td>
                <td>'.$column->Type.'</td>
                <td>'.($column->Null === "YES" ? "Yes" : "No").'</td>
                <td>'.($column->Default !== null ? $column->Default : "<em>None</em>").'</td>
              </tr>';
    }
    
    echo '</table>';
}

echo '</div>'; // End Database Structure card

// Message Statistics
if ($messagesTableExists) {
    echo '<div class="card">
            <h2>Message Statistics</h2>';
    
    $totalMessages = DB::table('messages')->count();
    $nullSenderType = $senderTypeExists ? DB::table('messages')->whereNull('sender_type')->count() : 0;
    $nullReceiverType = $receiverTypeExists ? DB::table('messages')->whereNull('receiver_type')->count() : 0;
    
    echo '<div class="check-item">
            <span>Total messages in database: <strong>'.$totalMessages.'</strong></span>
          </div>';
          
    if ($senderTypeExists && $receiverTypeExists) {
        echo '<div class="check-item">
                <span class="check-icon">'.($nullSenderType > 0 ? '⚠️' : '✅').'</span>
                <span>Messages with null sender_type: <span class="'.($nullSenderType > 0 ? 'warning' : 'success').'">'.$nullSenderType.'</span></span>
              </div>';
        
        echo '<div class="check-item">
                <span class="check-icon">'.($nullReceiverType > 0 ? '⚠️' : '✅').'</span>
                <span>Messages with null receiver_type: <span class="'.($nullReceiverType > 0 ? 'warning' : 'success').'">'.$nullReceiverType.'</span></span>
              </div>';
        
        if ($nullSenderType > 0 || $nullReceiverType > 0) {
            echo '<a href="fix_messages_table.php" class="fix-button">Fix Null Types</a>';
        }
    }
    
    // Check for potential UTF-8 encoding issues
    if (DB::connection()->getDriverName() === 'mysql') {
        try {
            $potentialIssues = DB::select("
                SELECT COUNT(*) as count FROM messages 
                WHERE message IS NOT NULL 
                AND message <> '' 
                AND message <> CONVERT(message USING utf8mb4)
            ");
            
            $issueCount = $potentialIssues[0]->count;
            
            echo '<div class="check-item">
                    <span class="check-icon">'.($issueCount > 0 ? '⚠️' : '✅').'</span>
                    <span>Messages with potential UTF-8 encoding issues: <span class="'.($issueCount > 0 ? 'warning' : 'success').'">'.$issueCount.'</span></span>
                  </div>';
        } catch (\Exception $e) {
            echo '<div class="check-item">
                    <span class="check-icon">❓</span>
                    <span class="warning">Could not check UTF-8 encoding issues: '.$e->getMessage().'</span>
                  </div>';
        }
    }
    
    echo '</div>'; // End Message Statistics card
    
    // Recent Messages
    echo '<div class="card">
            <h2>Recent Messages</h2>';
    
    if ($totalMessages > 0) {
        $recentMessages = Message::orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();
        
        echo '<table>
                <tr>
                    <th>ID</th>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Message</th>
                    <th>Read</th>
                    <th>Created</th>
                </tr>';
        
        foreach ($recentMessages as $message) {
            $senderName = User::find($message->sender_id)->name ?? 'Unknown';
            $receiverName = User::find($message->receiver_id)->name ?? 'Unknown';
            
            echo '<tr>
                    <td>'.$message->id.'</td>
                    <td>'.$senderName.' (ID: '.$message->sender_id.')'.
                        ($senderTypeExists ? '<br><small>'.$message->sender_type.'</small>' : '').'</td>
                    <td>'.$receiverName.' (ID: '.$message->receiver_id.')'.
                        ($receiverTypeExists ? '<br><small>'.$message->receiver_type.'</small>' : '').'</td>
                    <td>'.htmlspecialchars(substr($message->message, 0, 50)).
                        (strlen($message->message) > 50 ? '...' : '').'</td>
                    <td>'.($message->is_read ? 'Yes' : 'No').'</td>
                    <td>'.$message->created_at.'</td>
                  </tr>';
        }
        
        echo '</table>';
    } else {
        echo '<p>No messages found in the database.</p>';
    }
    
    echo '<a href="test_messages.php" class="fix-button">Run Message Test</a>';
    echo '</div>'; // End Recent Messages card
}

echo '</div>'; // End container
echo '</body></html>';
