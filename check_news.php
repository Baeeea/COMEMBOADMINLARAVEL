<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Database connection: " . config('database.default') . "\n";
echo "Database name: " . config('database.connections.' . config('database.default') . '.database') . "\n";

try {
    $newsCount = App\Models\News::count();
    echo "News count: " . $newsCount . "\n";
    
    if ($newsCount > 0) {
        $news = App\Models\News::all();
        echo "\nNews records:\n";
        foreach ($news as $item) {
            echo "ID: " . ($item->id ?? 'NULL') . ", Title: " . $item->Title . "\n";
        }
        
        // Check the actual table structure
        $firstNews = App\Models\News::first();
        echo "\nFirst news attributes:\n";
        foreach ($firstNews->getAttributes() as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No news records found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
