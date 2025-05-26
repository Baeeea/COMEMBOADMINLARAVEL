<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tables = DB::select('SHOW TABLES');
    echo "Available tables:\n";
    foreach($tables as $table) {
        $tableArray = (array)$table;
        echo current($tableArray) . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
