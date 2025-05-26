<?php
require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check database connection
try {
    \DB::connection()->getPdo();
    echo "Database connected successfully!\n";
    
    // Check if news table has data
    $newsCount = \DB::table("news")->count();
    echo "Current news count: " . $newsCount . "\n";
    
    // Add test data if none exists
    if ($newsCount == 0) {
        echo "Adding test data...\n";
        \DB::table("news")->insert([
            [
                "Title" => "Breaking News: Laravel Integration Complete",
                "content" => "We have successfully integrated the news management system.",
                "createdAt" => now(),
                "image" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "Title" => "Community Event Announcement",
                "content" => "Join us for the upcoming community gathering this weekend.",
                "createdAt" => now(),
                "image" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "Title" => "System Maintenance Notice",
                "content" => "The system will undergo maintenance on Sunday from 2-4 AM.",
                "createdAt" => now(),
                "image" => null,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
        echo "Test data added successfully!\n";
    }
    
    // Display current news
    $news = \DB::table("news")->get();
    echo "Current news items:\n";
    foreach ($news as $item) {
        echo "ID: " . $item->id . " - Title: " . $item->Title . "\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>