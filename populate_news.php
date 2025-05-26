<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear existing data
DB::table('news')->truncate();

// Insert sample data
$newsData = [
    [
        'Title' => 'Welcome to Comembo Barangay News',
        'content' => 'We are excited to share the latest updates and announcements from our barangay. Stay tuned for more important information.',
        'createdAt' => now()->subDays(5),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'Title' => 'Community Meeting Scheduled',
        'content' => 'A community meeting has been scheduled for next week to discuss important barangay matters. All residents are encouraged to attend.',
        'createdAt' => now()->subDays(3),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'Title' => 'New Health Programs Available',
        'content' => 'The barangay health center is now offering new health programs for residents. Please visit during operating hours for more information.',
        'createdAt' => now()->subDays(1),
        'created_at' => now(),
        'updated_at' => now()
    ]
];

foreach ($newsData as $data) {
    DB::table('news')->insert($data);
}

echo "News data populated successfully!\n";

// Verify the data
$news = DB::table('news')->get();
foreach ($news as $item) {
    echo "ID: {$item->id}, Title: {$item->Title}\n";
}
