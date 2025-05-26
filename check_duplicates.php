<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking for duplicate user_ids in complaintrequests table...\n";

try {
    $duplicates = DB::select('SELECT user_id, COUNT(*) as count FROM complaintrequests GROUP BY user_id HAVING COUNT(*) > 1');
    
    if (empty($duplicates)) {
        echo "No duplicate user_ids found.\n";
    } else {
        echo "Found duplicate user_ids:\n";
        foreach ($duplicates as $duplicate) {
            echo "user_id: {$duplicate->user_id}, count: {$duplicate->count}\n";
        }
    }
    
    echo "\nChecking total records...\n";
    $total = DB::selectOne('SELECT COUNT(*) as total FROM complaintrequests');
    echo "Total records: {$total->total}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
