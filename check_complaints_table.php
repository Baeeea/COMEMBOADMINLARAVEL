<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking if complaintrequests table exists...\n";
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('complaintrequests');
    echo "Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";

    if ($tableExists) {
        $count = \Illuminate\Support\Facades\DB::table('complaintrequests')->count();
        $pendingCount = \Illuminate\Support\Facades\DB::table('complaintrequests')->where('status', 'pending')->count();
        echo "Total records: " . $count . "\n";
        echo "Pending records: " . $pendingCount . "\n";
        
        echo "Table structure:\n";
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('complaintrequests');
        print_r($columns);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
