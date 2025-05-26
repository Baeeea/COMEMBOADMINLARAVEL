<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Populating news database with test data...\n";

try {
    // Clear existing records
    App\Models\News::truncate();
    echo "Cleared existing news records.\n";
    
    // Insert test news with proper IDs
    $newsData = [
        [
            'Title' => 'Community Clean-up Drive',
            'content' => 'Join us for a community-wide clean-up drive this Saturday. Help make our barangay cleaner and greener!',
            'createdAt' => '2025-05-20',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'Title' => 'Basketball Tournament Registration',
            'content' => 'Registration for the annual basketball tournament is now open. Sign up at the barangay hall.',
            'createdAt' => '2025-05-21',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'Title' => 'Health Check-up Schedule',
            'content' => 'Free health check-ups will be available at the barangay health center every Wednesday.',
            'createdAt' => '2025-05-22',
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];
    
    foreach ($newsData as $data) {
        App\Models\News::create($data);
    }
    
    echo "Successfully inserted " . count($newsData) . " news records.\n";
    
    // Verify records were created with proper IDs
    $news = App\Models\News::all();
    echo "\nNews records in database:\n";
    foreach ($news as $item) {
        echo "ID: {$item->id}, Title: {$item->Title}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
