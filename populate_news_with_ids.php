<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\News;

// Clear existing news to avoid conflicts
News::truncate();

// Create test news records
$newsData = [
    [
        'Title' => 'Community Health Fair',
        'content' => 'Join us for a free health fair this Saturday at the community center. Free health checkups and consultations will be available.',
        'createdAt' => '2025-05-20',
        'image' => null
    ],
    [
        'Title' => 'Barangay Meeting Schedule',
        'content' => 'Monthly barangay meeting scheduled for next Friday. All residents are encouraged to attend to discuss community matters.',
        'createdAt' => '2025-05-22',
        'image' => null
    ],
    [
        'Title' => 'New Garbage Collection Schedule',
        'content' => 'Please be informed that the garbage collection schedule has been updated. Collection will now be every Tuesday and Friday.',
        'createdAt' => '2025-05-24',
        'image' => null
    ]
];

foreach ($newsData as $data) {
    News::create($data);
}

echo "Created " . count($newsData) . " news records successfully!\n";

// Verify the records
$news = News::all();
echo "\nNews records in database:\n";
foreach ($news as $item) {
    echo "ID: " . $item->id . ", Title: " . $item->Title . "\n";
}
