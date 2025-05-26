<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test database connection
    $connection = DB::connection()->getPdo();
    echo "Connected to database: " . DB::connection()->getDatabaseName() . "\n";
    
    // Run migrations
    echo "Running migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    // Add test data
    echo "Adding test news items...\n";
    DB::table('news')->insert([
        [
            'Title' => 'Community Event Announcement',
            'content' => 'Join us for the upcoming community gathering this weekend.',
            'createdAt' => now(),
            'image' => null,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'Title' => 'New Barangay Initiatives', 
            'content' => 'Learn about the new programs being launched in our barangay.',
            'createdAt' => now(),
            'image' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);
    
    echo "Added test news items successfully.\n";
    
    // Verify data
    $newsCount = App\Models\News::count();
    echo "Total news items: $newsCount\n";
    
    $newsList = App\Models\News::all();
    foreach ($newsList as $news) {
        echo "ID: {$news->id}, Title: {$news->Title}\n";
    }
    
} catch (\Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
