<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding test news data using Laravel Eloquent...\n";

try {
    // Test database connection
    $connection = \Illuminate\Support\Facades\DB::connection();
    echo "Database connection: " . $connection->getDatabaseName() . "\n";
    
    // Clear existing news
    \App\Models\News::truncate();
    echo "Cleared existing news records.\n";
    
    // Create test news records
    $newsData = [
        [
            'Title' => 'Community Clean-up Drive',
            'content' => 'Join us for a community-wide clean-up drive this Saturday. Help make our barangay cleaner and greener!',
            'createdAt' => '2025-05-20',
        ],
        [
            'Title' => 'Basketball Tournament Registration',
            'content' => 'Registration for the annual basketball tournament is now open. Sign up at the barangay hall.',
            'createdAt' => '2025-05-21',
        ],
        [
            'Title' => 'Health Check-up Schedule',
            'content' => 'Free health check-ups will be available at the barangay health center every Wednesday.',
            'createdAt' => '2025-05-22',
        ]
    ];
    
    foreach ($newsData as $data) {
        \App\Models\News::create($data);
    }
    
    echo "✓ Created " . count($newsData) . " news records!\n";
    
    // Verify records
    $news = \App\Models\News::all();
    echo "\nNews records in database:\n";
    foreach ($news as $item) {
        echo "ID: {$item->id}, Title: {$item->Title}\n";
    }
    
    echo "\n✓ Test data ready! You can now access your Laravel app.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
