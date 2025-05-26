<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test database connection
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    echo "Connected to database: " . $databaseName . "\n";
    
    // Test if news table exists
    $tables = DB::select("SHOW TABLES");
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- " . $tableName . "\n";
    }
    
    // Check news table structure if it exists
    try {
        $columns = DB::select("DESCRIBE news");
        echo "\nNews table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
        
        // Check news records
        $newsCount = DB::table('news')->count();
        echo "\nNumber of news records: " . $newsCount . "\n";
        
        if ($newsCount > 0) {
            $news = DB::table('news')->get();
            echo "\nNews records:\n";
            foreach ($news as $item) {
                echo "ID: {$item->id}, Title: {$item->Title}\n";
            }
        }
        
    } catch (Exception $e) {
        echo "News table doesn't exist or error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
